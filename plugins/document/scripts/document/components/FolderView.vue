<!--
  - Copyright (c) Enalean, 2018. All Rights Reserved.
  -
  - This file is a part of Tuleap.
  -
  - Tuleap is free software; you can redistribute it and/or modify
  - it under the terms of the GNU General Public License as published by
  - the Free Software Foundation; either version 2 of the License, or
  - (at your option) any later version.
  -
  - Tuleap is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License
  - along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <div>
        <h1 v-translate>Documents</h1>
        <folder-loading-screen v-if="is_loading"/>
        <div class="tlp-card" v-if="has_loaded_without_error">
            <div class="empty-pane">
                <div class="empty-page-illustration">
                    <empty-folder-svg/>
                </div>
                <p class="empty-page-text" v-translate>It's time to add new documents!</p>
                <button type="button" class="tlp-button-primary tlp-button-large" disabled>
                    <i class="fa fa-plus tlp-button-icon"></i>
                    <translate>New document</translate>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from "vuex";

import FolderLoadingScreen from "./Folder/FolderLoadingScreen.vue";
import EmptyFolderSvg from "./Folder/EmptyFolderSvg.vue";

export default {
    name: "FolderView",
    components: { FolderLoadingScreen, EmptyFolderSvg },
    computed: {
        ...mapState({
            has_loaded_without_error: (state, getters) =>
                !state.is_loading_root_document && !getters.has_error,
            is_loading: state => state.is_loading_root_document
        })
    }
};
</script>
