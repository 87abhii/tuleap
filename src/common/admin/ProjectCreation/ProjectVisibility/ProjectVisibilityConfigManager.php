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

namespace Tuleap\admin\ProjectCreation\ProjectVisibility;

use ForgeConfig;

class ProjectVisibilityConfigManager
{
    const PROJECT_ADMIN_CAN_CHOOSE_VISIBILITY = 'project_admin_can_choose_visibility';
    /**
     * @var \ConfigDao
     */
    private $config_dao;

    public function __construct(\ConfigDao $config_dao)
    {
        $this->config_dao = $config_dao;
    }

    public function updateProjectAdminVisibility($new_value)
    {
        $old_value = (bool) ForgeConfig::get(ProjectVisibilityConfigManager::PROJECT_ADMIN_CAN_CHOOSE_VISIBILITY);

        if ($old_value === $new_value) {
            return;
        }

        $this->config_dao->save(self::PROJECT_ADMIN_CAN_CHOOSE_VISIBILITY, $new_value);
    }
}
