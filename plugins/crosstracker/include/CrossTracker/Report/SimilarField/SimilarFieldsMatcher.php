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

namespace Tuleap\CrossTracker\Report\SimilarField;

use Tuleap\CrossTracker\CrossTrackerReport;

class SimilarFieldsMatcher
{
    /** @var SupportedFieldsDao */
    private $similar_fields_dao;
    /** @var \Tracker_FormElementFactory */
    private $form_element_factory;

    public function __construct(SupportedFieldsDao $similar_fields_dao, \Tracker_FormElementFactory $form_element_factory)
    {
        $this->similar_fields_dao   = $similar_fields_dao;
        $this->form_element_factory = $form_element_factory;
    }

    /**
     * @param CrossTrackerReport $report
     * @return SimilarFieldCollection
     */
    public function getSimilarFieldsCollection(CrossTrackerReport $report, \PFUser $user)
    {
        $rows = $this->similar_fields_dao->searchByTrackerIds($report->getTrackerIds());

        $similar_field_candidates = [];
        foreach ($rows as $row) {
            $field                      = $this->form_element_factory->getCachedInstanceFromRow($row);
            $similar_field_candidates[] = new SimilarFieldCandidate($row['formElement_type'], $field);
        }
        $similar_fields_without_permissions_verification = new SimilarFieldCollection(...$similar_field_candidates);

        $similar_field_candidates_with_permissions_verification = [];
        foreach ($similar_fields_without_permissions_verification as $similar_field_candidate) {
            if ($similar_field_candidate->getField()->userCanRead($user)) {
                $similar_field_candidates_with_permissions_verification[] = $similar_field_candidate;
            }
        }

        return new SimilarFieldCollection(...$similar_field_candidates_with_permissions_verification);
    }
}
