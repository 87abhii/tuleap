{*
 * Copyright (c) Enalean, 2018. All Rights Reserved.
 * Copyright (C) 2009 Christopher Han <xiphux@gmail.com>
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

<section class="tlp-pane-section">
    <div class="git-repository-blob-header">
        <div>
            {assign var=blobcommit value=$blob->GetCommit()}
            {assign var=blobtree value=$blobcommit->GetTree()}

            <a href="{$SCRIPT_NAME}?a=tree&amp;hb={$blobcommit->GetHash()|urlencode}&amp;h={$blobtree->GetHash()|urlencode}">{$project->GetProject()|escape}</a>/<!--
            -->{foreach from=$blob->GetPathTree() item=pathtreepiece}<!--
                --><a href="{$SCRIPT_NAME}?a=tree&amp;hb={$blobcommit->GetHash()|urlencode}&amp;h={$pathtreepiece->GetHash()|urlencode}&amp;f={$pathtreepiece->GetPath()|urlencode}">{$pathtreepiece->GetName()|escape}</a>/<!--
            -->{/foreach}<!--
            -->{if $blob->isBlob()}<!--
                --><a href="{$SCRIPT_NAME}?a=blob&amp;h={$blob->GetHash()|urlencode}&amp;hb={$blobcommit->GetHash()|urlencode}&amp;f={$blob->GetPath()|urlencode}">{$blob->GetName()|escape}</a>
            {/if}
        </div>

        <div class="git-repository-blob-header-actions">
            <div class="tlp-button-bar">
                <div class="tlp-button-bar-item">
                    <a href="{$SCRIPT_NAME}?a=blob_plain&amp;h={$blob->GetHash()|urlencode}&amp;f={$blob->GetPath()|urlencode}&amp;noheader=1"
                        class="tlp-button-primary tlp-button-outline tlp-button-small"
                    >
                        {t}Plain{/t}
                    </a>
                </div>
                {if $blob->GetPath()}
                    {if !$datatag}
                        <div class="tlp-button-bar-item">
                            <a href="{$SCRIPT_NAME}?a=blame&amp;h={$blob->GetHash()|urlencode}&amp;f={$blob->GetPath()|urlencode}&amp;hb={$commit->GetHash()|urlencode}"
                                class="tlp-button-primary tlp-button-outline tlp-button-small"
                            >
                                {t}Blame{/t}
                            </a>
                        </div>
                    {/if}
                    <div class="tlp-button-bar-item">
                        <a href="{$SCRIPT_NAME}?a=history&amp;h={$commit->GetHash()|urlencode}&amp;f={$blob->GetPath()|urlencode}"
                            class="tlp-button-primary tlp-button-outline tlp-button-small"
                        >
                            {t}History{/t}
                        </a>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</section>
