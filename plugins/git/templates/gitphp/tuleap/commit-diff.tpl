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

    {include file='tuleap/commit-title-metadata.tpl'}
</section>
<section class="tlp-pane-section">
    <h2 class="tlp-pane-subtitle">
        <i class="tlp-pane-title-icon fa fa-copy"></i> {t}Modified Files{/t}
    </h2>
    <div class="git-repository-commit-diff-actions">
        <div class="tlp-button-bar">
            <div class="tlp-button-bar-item">
                <a href="{$commit_presenter->getCommitListLink()}"
                   class="tlp-button-primary tlp-button-outline tlp-button-small"
                >
                    <i class="fa fa-list tlp-button-icon"></i> {t}List{/t}
                </a>
            </div>
            <div class="tlp-button-bar-item">
                <input type="radio" class="tlp-button-bar-checkbox" checked>
                <label class="tlp-button-primary tlp-button-outline tlp-button-small">
                    <i class="fa fa-list-alt tlp-button-icon"></i> {t}Inline diff{/t}
                </label>
            </div>
        </div>
    </div>
    {foreach from=$treediff item=filediff}
        <div class="git-repository-commit-diff-file-header">
            <span class="{$commit_presenter->getStatusClassname($filediff)} git-repository-commit-diff-file-header-element"
            >{$filediff->GetStatus()|escape}</span>
            <a href="{$commit_presenter->getDiffLink($filediff)}"
               class="git-repository-commit-diff-file-header-element"
            >{$filediff->GetFromFile()|escape}</a>
            <div class="git-repository-commit-diff-file-header-spacer"></div>
            <span class="git-repository-commit-file-stat-added git-repository-commit-diff-file-header-element">
                {if (! empty($filediff->hasStats()))}
                    +{$filediff->getAddedStats()}
                {/if}
            </span>
            <span class="git-repository-commit-file-stat-removed git-repository-commit-diff-file-header-element">
                {if (! empty($filediff->hasStats()))}
                    −{$filediff->getRemovedStats()}
                {/if}
            </span>
        </div>
        <div class="diffBlob">
            {include file='tuleap/file-diff.tpl' diff=$filediff->GetDiff('', true, true)}
        </div>
    {/foreach}
