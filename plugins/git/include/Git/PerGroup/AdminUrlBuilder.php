<?php
/**
 * Copyright (c) Enalean, 2018. All Rights Reserved.
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

namespace Tuleap\Git\PerGroup;

use GitRepository;
use Project;

class AdminUrlBuilder
{
    public function buildAdminUrl(GitRepository $repository, Project $project)
    {
        $admin_url_params = http_build_query(
            [
                'action'   => 'repo_management',
                'group_id' => $project->getID(),
                'repo_id'  => $repository->getId(),
                'pane'     => 'perms'
            ]
        );
        return GIT_BASE_URL . '/?' . $admin_url_params;
    }
}
