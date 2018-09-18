<?php
/**
 * Copyright (c) Enalean, 2014-2018. All Rights Reserved.
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

require_once dirname(__FILE__) .'/../include/autoload.php';
require_once 'SystemEvent_FULLTEXTSEARCH_DOCMANTest.class.php';

class SystemEvent_FULLTEXTSEARCH_DOCMAN_WIKI_INDEXTest extends SystemEvent_FULLTEXTSEARCH_DOCMANTest {

    protected $klass = 'SystemEvent_FULLTEXTSEARCH_DOCMAN_WIKI_INDEX';

    public function aSystemEventWithParameter($parameters) {
        $id = $type = $owner = $priority = $status = $create_date = $process_date = $end_date = $log = null;
        $event = partial_mock(
            'SystemEvent_FULLTEXTSEARCH_DOCMAN_WIKI_INDEX',
            array('getWikiPage'),
            array($id, $type, $owner, $parameters, $priority, $status, $create_date, $process_date, $end_date, $log)
        );

        $this->wiki_page = stub(\Tuleap\PHPWiki\WikiPage::class)->getMetadata()->returns(array());
        stub($event)->getWikiPage()->returns($this->wiki_page);

        $event->injectDependencies($this->actions, $this->item_factory, $this->version_factory, $this->link_version_factory);
        return $event;
    }

    public function itAsksToToFullTextSearchActionsIfProjectMappingAlreadyExists() {
        $event = $this->aSystemEventWithParameter('101::103::2');
        expect($this->actions)->checkProjectMappingExists(101)->once();

        $this->assertTrue($event->process());
    }

    public function itAddsDefaultProjectMappingIfProjectMappingDoesNotExist() {
        $event = $this->aSystemEventWithParameter('101::103::2');
        stub($this->actions)->checkProjectMappingExists(101)->returns(false);

        expect($this->actions)->initializeProjetMapping(101)->once();

        $this->assertTrue($event->process());
    }

    public function itDelegatesIndexingToFullTextSearchActions() {
        $event = $this->aSystemEventWithParameter('101::103::2');
        stub($this->actions)->indexNewWikiDocument($this->item, array())->once();
        $this->assertTrue($event->process());
        $this->assertEqual($event->getLog(), 'OK');
        $this->assertEqual($event->getStatus(), SystemEvent::STATUS_DONE);
    }
}