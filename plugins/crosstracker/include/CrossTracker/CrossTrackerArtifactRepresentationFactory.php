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

namespace Tuleap\CrossTracker;

use Tuleap\CrossTracker\Report\CSV\CSVRepresentation;
use Tuleap\CrossTracker\Report\CSV\CSVRepresentationBuilder;
use Tuleap\CrossTracker\Report\CSV\PaginatedCollectionOfCSVRepresentations;
use Tuleap\CrossTracker\REST\v1\CrossTrackerArtifactReportRepresentation;
use Tuleap\CrossTracker\REST\v1\PaginatedCollectionOfCrossTrackerArtifacts;
use Tuleap\Tracker\REST\v1\ArtifactMatchingReportCollection;

class CrossTrackerArtifactRepresentationFactory
{
    /**
     * @var CSVRepresentationBuilder
     */
    private $csv_builder;

    public function __construct(CSVRepresentationBuilder $csv_builder)
    {
        $this->csv_builder = $csv_builder;
    }

    /**
     * @return PaginatedCollectionOfCrossTrackerArtifacts
     */
    public function buildRepresentationsForReport(ArtifactMatchingReportCollection $collection, \PFUser $current_user)
    {
        $representations = [];
        foreach ($collection->getArtifacts() as $artifact) {
            if (! $artifact->userCanView($current_user)) {
                continue;
            }
            $artifact_representation = new CrossTrackerArtifactReportRepresentation();
            $artifact_representation->build($artifact, $current_user);
            $representations[] = $artifact_representation;
        }

        return new PaginatedCollectionOfCrossTrackerArtifacts($representations, $collection->getTotalSize());
    }

    public function buildRepresentationsForCSV(ArtifactMatchingReportCollection $collection, \PFUser $current_user)
    {
        $representations = [$this->csv_builder->buildHeaderLine($current_user)];

        foreach ($collection->getArtifacts() as $artifact) {
            if (! $artifact->userCanView($current_user)) {
                continue;
            }

            $representations[] = $this->csv_builder->build($artifact, $current_user);
        }

        return new PaginatedCollectionOfCSVRepresentations($representations, $collection->getTotalSize());
    }
}
