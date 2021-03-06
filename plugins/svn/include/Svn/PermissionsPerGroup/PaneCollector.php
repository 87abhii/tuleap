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

namespace Tuleap\Svn\PermissionsPerGroup;

use ForgeConfig;
use SvnPlugin;
use TemplateRendererFactory;
use Tuleap\Project\Admin\PermissionsPerGroup\PermissionPerGroupPaneCollector;
use UGroupManager;

class PaneCollector
{
    /**
     * @var UGroupManager
     */
    private $ugroup_manager;
    /**
     * @var PermissionPerGroupSVNServicePaneBuilder
     */
    private $group_pane_builder;

    public function __construct(
        UGroupManager $ugroup_manager,
        PermissionPerGroupSVNServicePaneBuilder $group_pane_builder
    ) {
        $this->ugroup_manager          = $ugroup_manager;
        $this->group_pane_builder      = $group_pane_builder;
    }

    public function collectPane(PermissionPerGroupPaneCollector $event)
    {
        $service_presenter = $this->group_pane_builder->buildPresenter($event);

        $templates_dir = ForgeConfig::get('tuleap_dir') . '/plugins/svn/templates/';
        $content       = TemplateRendererFactory::build()
            ->getRenderer($templates_dir)
            ->renderToString('project-admin-permission-per-group', $service_presenter);

        $project         = $event->getProject();
        $rank_in_project = $project->getService(
            SvnPlugin::SERVICE_SHORTNAME
        )->getRank();

        $event->addPane($content, $rank_in_project);
    }
}
