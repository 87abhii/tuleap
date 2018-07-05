<?php
/**
 * Copyright (c) Enalean, 2011 - 2018. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

class GitRepositoryFactory
{
    /**
     * @var GitDao
     */
    private $dao;

    /**
     * @var ProjectManager
     */
    private $projectManager;

    public function __construct(GitDao $dao, ProjectManager $projectManager) {
        $this->dao            = $dao;
        $this->projectManager = $projectManager;
    }

    /**
     * Get a project repository by its id
     *
     * @param int $id         The id of the repository to load
     *
     * @return GitRepository the repository or null if not found
     */
    public function getRepositoryById($id) {
        if ($id == GitRepositoryGitoliteAdmin::ID) {
            return new GitRepositoryGitoliteAdmin();
        }
        $row = $this->dao->searchProjectRepositoryById($id);
        return $this->getRepositoryFromRow($row);
    }

    /**
     * Get a project repository by its id
     *
     * @return GitRepository the repository or null if not found
     */
    public function getRepositoryByIdUserCanSee(PFUser $user, $id) {
        if ($id == GitRepositoryGitoliteAdmin::ID) {
            return new GitRepositoryGitoliteAdmin();
        }

        $dar        = $this->dao->searchProjectRepositoryById($id);
        $repository = $this->getRepositoryFromRow($dar);

        if ($repository === null) {
            throw new GitRepoNotFoundException();
        }

        $project = $repository->getProject();
        $url_verification = new URLVerification();
        try {
            $url_verification->userCanAccessProject($user, $project);
        } catch (Exception $exception) {
            throw $exception;
        }

        if (! $repository->userCanRead($user)) {
            throw new GitRepoNotReadableException();
        }

        return $repository;
    }

    /**
     * Return all git repositories of a project (gitshell, gitolite, personal forks)
     *
     * @param Project $project
     *
     * @return GitRepository[]
     */
    public function getAllRepositories(Project $project) {
        $repositories = array();
        $repository_list = $this->dao->getProjectRepositoryList($project->getID(), false, false);
        foreach ($repository_list as $row) {
            $repository = new GitRepository();
            $this->dao->hydrateRepositoryObject($repository, $row);
            $repositories[] = $repository;
        }
        return $repositories;
    }

    public function getAllRepositoriesUserCanSee(Project $project, PFUser $user) {
        $repositories = $this->getAllRepositories($project);
        foreach ($repositories as $key => $repository) {
            if (! $repository->userCanRead($user)) {
                unset($repositories[$key]);
            }
        }

        return $repositories;
    }

    /**
     * @param Project $project
     * @param PFuser $user
     * @param int $limit
     * @param int $offset
     * @return GitRepository[]
     */
    public function getPaginatedRepositoriesUserCanSee(Project $project, PFuser $user, $scope, $limit, $offset)
    {
        $repositories    = [];
        $repository_list = $this->dao->getPaginatedOpenRepositories($project->getID(), $scope, $limit, $offset);
        foreach ($repository_list as $row) {
            $repository = $this->getRepositoryFromRow($row);
            if ($repository->userCanRead($user)) {
                $repositories[] = $repository;
            }
        }
        return $repositories;
    }

    /**
     * Get a deleted repository by its id
     *
     * @param int $id         The id of the repository to load
     *
     * @return GitRepository the repository or null if not found
     */
    public function getDeletedRepository($id) {
        $row = $this->dao->searchDeletedRepositoryById($id);
        return $this->getRepositoryFromRow($row);
    }

    /**
     * Get a project repository by its id
     *
     * @param int $id         The id of the repository to load
     *
     * @return GitRepository the repository or null if not found
     */
    public function getRepositoryByPath($project_id, $path) {
        $row = $this->dao->searchProjectRepositoryByPath($project_id, $path);
        return $this->getRepositoryFromRow($row);
    }

    /**
     * Return the repository given it's full path on the file system (from /)
     *
     * @param String $full_path
     *
     * @return GitRepository
     */
    public function getFromFullPath($full_path) {
        $repo = $this->getByRepositoryRootMatch('gitolite/repositories', $full_path);
        if (!$repo) {
            $repo = $this->getByRepositoryRootMatch('gitroot', $full_path);
        }
        return $repo;
    }

    /**
     * Return all repositories with a remote server set
     *
     * @return Array of GitRepository
     */
    public function getActiveRepositoriesWithRemoteServersForAllProjects() {
        $repositories = array();
        foreach ($this->dao->getActiveRepositoryPathsWithRemoteServersForAllProjects() as $row) {
            $repository = new GitRepository();
            $this->dao->hydrateRepositoryObject($repository, $row);
            $repository->setProject($this->projectManager->getProject($row[GitDao::FK_PROJECT_ID]));
            $repositories[] = $repository;
        }
        return $repositories;
    }

    /**
     * @todo should be private
     *
     * @param Project $project
     * @param ProjectUGroup $ugroup
     * @param PFUser $user
     * @return \GitRepositoryWithPermissions
     */
    public function getGerritRepositoriesWithPermissionsForUGroupAndProject(Project $project, ProjectUGroup $ugroup, PFUser $user) {
        $repositories = array();
        $ugroups      = $user->getUgroups($project->getID(), null);
        $ugroups[]    = $ugroup->getId();
        $dar          = $this->dao->searchGerritRepositoriesWithPermissionsForUGroupAndProject($project->getID(), $ugroups);
        foreach ($dar as $row) {
            if (isset($repositories[$row['repository_id']])) {
                $repo_with_perms = $repositories[$row['repository_id']];
            } else {
                $repo_with_perms = new GitRepositoryWithPermissions($this->instanciateFromRow($row));
                $repositories[$row['repository_id']] = $repo_with_perms;
            }
            $repo_with_perms->addUGroupForPermissionType($row['permission_type'], $row['ugroup_id']);

        }
        return $repositories;
    }

    public function getAllGerritRepositoriesFromProject(Project $project, PFUser $user) {
        $all_repositories_dar = $this->dao->searchAllGerritRepositoriesOfProject($project->getId());
        $all_repositories     = array();

        if (count($all_repositories_dar) == 0) {
            return array();
        }

        foreach ($all_repositories_dar as $row) {
            $all_repositories[$row['repository_id']] = new GitRepositoryWithPermissions($this->instanciateFromRow($row));
        }
        $admin_ugroup = new ProjectUGroup(array('ugroup_id' => ProjectUGroup::PROJECT_ADMIN));
        $repositories_with_admin_permissions = $this->getGerritRepositoriesWithPermissionsForUGroupAndProject($project, $admin_ugroup, $user);

        foreach ($repositories_with_admin_permissions as $repository_id => $repository) {
            $all_repositories[$repository_id] = $repository;
        }

        foreach ($all_repositories as $repository) {
            $repository->addUGroupForPermissionType(Git::SPECIAL_PERM_ADMIN, ProjectUGroup::PROJECT_ADMIN);
        }

        return $all_repositories;

    }

    /**
     * Attempt to get repository if path match given base directory
     *
     * @param type $base_dir A top level directory that can contains repo
     * @param type $path     Full repository path
     *
     * @return GitRepository
     */
    private function getByRepositoryRootMatch($base_dir, $path) {
        $matches = array();
        if (preg_match('%'.$base_dir.'/([^/]+)/(.*)$%', $path, $matches)) {
            return $this->getByProjectNameAndPath($matches[1], $matches[2]);
        }
        return null;
    }

    /**
     *
     * @param String $projectName
     * @param String $path
     *
     * @return GitRepository
     */
    public function getByProjectNameAndPath($projectName, $path) {
        $project = $this->projectManager->getProjectByUnixName($projectName);
        if ($project) {
            return $this->getRepositoryByPath($project->getID(), $projectName . '/' . $path);
        }
        return null;
    }

    /**
     * @return GitRepository|null
     */
    private function getRepositoryFromRow($row)
    {
        if (empty($row)) {
            return null;
        }
        return $this->instanciateFromRow($row);
    }

    public function instanciateFromRow(array $row) {
        $repository = new GitRepository();
        $this->dao->hydrateRepositoryObject($repository, $row);
        return $repository;
    }

    /**
     * Get the list of all repositories of a project
     *
     * @return GitRepository[]
     */
    public function getAllRepositoriesOfProject(Project $project) {
        $repositories = [];

        $rows = $this->dao->getAllGitoliteRespositories($project->getID());
        foreach ($rows as $row) {
            $repositories[] = $this->instanciateFromRow($row);
        }

        return $repositories;
    }

    /**
     * Get the list of all archived repositories to purge
     *
     * @param int $retention_period
     *
     * @return GitRepository[]
     */
    public function getArchivedRepositoriesToPurge($retention_period) {
        $archived_repositories = array();
        $deleted_repositories  = $this->dao->getDeletedRepositoriesToPurge($retention_period);
        foreach ($deleted_repositories as $deleted_repository) {
            $repository = $this->instanciateFromRow($deleted_repository);
            array_push($archived_repositories, $repository);
        }
        return $archived_repositories;
    }

    /**
     * Get the list of all repositories for a given project
     *
     * @param Int $project_id
     *
     * @return GitRepository[]
     */
    public function getDeletedRepositoriesByProjectId($project_id, $retention_period) {
        $repositories         = array();
        $deleted_repositories = $this->dao->getDeletedRepositoriesByProjectId($project_id, $retention_period);
        foreach ($deleted_repositories as $deleted_repository) {
            $repository = $this->instanciateFromRow($deleted_repository);
            array_push($repositories, $repository);
        }
        return $repositories;
    }

    public function hasGitShellRepositories() {
        return $this->dao->hasGitShellRepositories();
    }

    /**
     * Returns a default-value-filled repository
     * @return GitRepository
     */
    public function buildRepository(Project $project, $repository_name, PFUser $creator, Git_Backend_Interface $backend, $description = GitRepository::DEFAULT_DESCRIPTION) {
        $repository = new GitRepository();
        $repository->setBackend($backend);
        $repository->setDescription($description);
        $repository->setCreator($creator);
        $repository->setProject($project);
        $repository->setName(preg_replace('/\/+/','/', $repository_name));
        return $repository;
    }
}
