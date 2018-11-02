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

namespace Tuleap\CrossTracker\Report\CSV\Format;

use RuntimeException;
use Tracker_FormElement_Field_ArtifactId;
use Tracker_FormElement_Field_ArtifactLink;
use Tracker_FormElement_Field_Burndown;
use Tracker_FormElement_Field_Checkbox;
use Tracker_FormElement_Field_Computed;
use Tracker_FormElement_Field_CrossReferences;
use Tracker_FormElement_Field_Date;
use Tracker_FormElement_Field_File;
use Tracker_FormElement_Field_Float;
use Tracker_FormElement_Field_Integer;
use Tracker_FormElement_Field_LastModifiedBy;
use Tracker_FormElement_Field_LastUpdateDate;
use Tracker_FormElement_Field_MultiSelectbox;
use Tracker_FormElement_Field_OpenList;
use Tracker_FormElement_Field_PermissionsOnArtifact;
use Tracker_FormElement_Field_PerTrackerArtifactId;
use Tracker_FormElement_Field_Radiobutton;
use Tracker_FormElement_Field_Selectbox;
use Tracker_FormElement_Field_String;
use Tracker_FormElement_Field_SubmittedBy;
use Tracker_FormElement_Field_SubmittedOn;
use Tracker_FormElement_Field_Text;
use Tuleap\Tracker\FormElement\TrackerFormElementExternalField;

class FormElementToValueVisitor implements \Tracker_FormElement_FieldVisitor
{
    public function visitArtifactLink(Tracker_FormElement_Field_ArtifactLink $field)
    {
        throw new RuntimeException("Artifact link field is not supported for similar fields matching.");
    }

    public function visitDate(Tracker_FormElement_Field_Date $field)
    {
        throw new RuntimeException("Date field is not supported for similar fields matching.");
    }

    public function visitFile(Tracker_FormElement_Field_File $field)
    {
        throw new RuntimeException("File field is not supported for similar fields matching.");
    }

    public function visitFloat(Tracker_FormElement_Field_Float $field)
    {
        throw new RuntimeException("Float field is not supported for similar fields matching.");
    }

    public function visitInteger(Tracker_FormElement_Field_Integer $field)
    {
        throw new RuntimeException("Integer field is not supported for similar fields matching.");
    }

    public function visitOpenList(Tracker_FormElement_Field_OpenList $field)
    {
        throw new RuntimeException("Open list field is not supported for similar fields matching.");
    }

    public function visitPermissionsOnArtifact(Tracker_FormElement_Field_PermissionsOnArtifact $field)
    {
        throw new RuntimeException("Permission field is not supported for similar fields matching.");
    }

    public function visitString(Tracker_FormElement_Field_String $field)
    {
        return new TextValue();
    }

    public function visitText(Tracker_FormElement_Field_Text $field)
    {
        return new TextValue();
    }

    public function visitRadiobutton(Tracker_FormElement_Field_Radiobutton $field)
    {
        throw new RuntimeException("Radio button field is not supported for similar fields matching.");
    }

    public function visitCheckbox(Tracker_FormElement_Field_Checkbox $field)
    {
        throw new RuntimeException("Checkbox field is not supported for similar fields matching.");
    }

    public function visitMultiSelectbox(Tracker_FormElement_Field_MultiSelectbox $field)
    {
        throw new RuntimeException("Multi-selectbox field is not supported for similar fields matching.");
    }

    public function visitSelectbox(Tracker_FormElement_Field_Selectbox $field)
    {
        throw new RuntimeException("Selectbox field is not supported for similar fields matching.");
    }

    public function visitSubmittedBy(Tracker_FormElement_Field_SubmittedBy $field)
    {
        throw new RuntimeException("Matching always-there fields should already be done in another step.");
    }

    public function visitLastModifiedBy(Tracker_FormElement_Field_LastModifiedBy $field)
    {
        throw new RuntimeException("Matching always-there fields should already be done in another step.");
    }

    public function visitArtifactId(Tracker_FormElement_Field_ArtifactId $field)
    {
        throw new RuntimeException("Matching always-there fields should already be done in another step.");
    }

    public function visitPerTrackerArtifactId(Tracker_FormElement_Field_PerTrackerArtifactId $field)
    {
        throw new RuntimeException("Matching always-there fields should already be done in another step.");
    }

    public function visitCrossReferences(Tracker_FormElement_Field_CrossReferences $field)
    {
        throw new RuntimeException("Cross references field is not supported for similar fields matching.");
    }

    public function visitBurndown(Tracker_FormElement_Field_Burndown $field)
    {
        throw new RuntimeException("Burndown field is not supported for similar fields matching.");
    }

    public function visitLastUpdateDate(Tracker_FormElement_Field_LastUpdateDate $field)
    {
        throw new RuntimeException("Matching always-there fields should already be done in another step.");
    }

    public function visitSubmittedOn(Tracker_FormElement_Field_SubmittedOn $field)
    {
        throw new RuntimeException("Matching always-there fields should already be done in another step.");
    }

    public function visitComputed(Tracker_FormElement_Field_Computed $field)
    {
        throw new RuntimeException("Computed field is not supported for similar fields matching.");
    }

    public function visitExternalField(TrackerFormElementExternalField $element)
    {
        throw new RuntimeException("External field is not supported for similar fields matching.");
    }
}
