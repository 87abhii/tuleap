{*
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
 *}

<h2 class="tlp-pane-subtitle">
    <i class="tlp-pane-title-icon fa fa-copy"></i> {t}Modified Files{/t}
</h2>
<div class="tlp-table-actions">
    <div class="tlp-table-actions-spacer"></div>
    <div class="tlp-button-bar tlp-table-actions-element">
        <div class="tlp-button-bar-item">
            <input type="radio" class="tlp-button-bar-checkbox" checked>
            <label class="tlp-button-primary tlp-button-outline tlp-button-small">
                <i class="fa fa-list tlp-button-icon"></i> {t}List{/t}
            </label>
        </div>
        <div class="tlp-button-bar-item">
            <a href="{$commit_presenter->getCommitDiffLink()}"
               class="tlp-button-primary tlp-button-outline tlp-button-small"
            >
                <i class="fa fa-list-alt tlp-button-icon"></i> {t}Inline diff{/t}
            </a>
        </div>
    </div>
</div>
<table class="tlp-table">
    <thead>
        <tr>
            <th></th>
            <th>{t}Name{/t}</th>
            <th class="tlp-table-cell-numeric"></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    {foreach from=$treediff item=diffline}
        <tr>
            <td class="{$commit_presenter->getStatusClassname($diffline)}">{$diffline->GetStatus()|escape}</td>
            <td>{$diffline->GetFromFile()|escape}</td>
            {if (! $diffline->isBinaryFile())}
                <td class="tlp-table-cell-numeric git-repository-commit-file-stat-added">
                    {if (! empty($diffline->hasStats()))}
                        +{$diffline->getAddedStats()}
                    {/if}
                </td>
                <td class="git-repository-commit-file-stat-removed">
                    {if (! empty($diffline->hasStats()))}
                        −{$diffline->getRemovedStats()}
                    {/if}
                </td>
            {/if}
            {if ($diffline->isBinaryFile())}
                <td class="git-repository-commit-file-stat-binary" colspan="2">
                    {t}Binary file{/t}
                </td>
            {/if}
            <td class="tlp-table-cell-actions">
                <a href="{$commit_presenter->getDiffLink($diffline)}"
                   class="tlp-table-cell-actions-button tlp-button-primary tlp-button-outline tlp-button-small"
                >
                    <i class="fa fa-long-arrow-right tlp-button-icon"></i> {t}Go to diff{/t}
                </a>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
