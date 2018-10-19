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

namespace Tuleap\Error;

use Error_PermissionDenied;
use HTTPRequest;
use Tuleap\Layout\BaseLayout;
use Tuleap\Request\DispatchableWithRequest;
use UnexpectedValueException;
use Url;
use Valid_Text;

class PermissionDeniedMailSender extends Error_PermissionDenied implements DispatchableWithRequest
{
    /**
     * @var PlaceHolderBuilder
     */
    private $place_holder_builder;

    public function __construct(PlaceHolderBuilder $place_holder_builder, Url $url = null)
    {
        parent::__construct($url);
        $this->place_holder_builder = $place_holder_builder;
    }

    public function process(HTTPRequest $request, BaseLayout $layout, array $variables)
    {
        $this->getToken()->check("/my/");

        $valid_message = new Valid_Text('msg_private_project');
        $valid_message->required();
        if (! $request->valid($valid_message)) {
            throw new UnexpectedValueException(_("Message sent to administrator should not be empty."));
        }

        if ($request->get('msg_private_project') === $this->place_holder_builder->buildPlaceHolder($request->getProject())) {
            throw new UnexpectedValueException(_("Message sent to administrator should not be empty."));
        }

        $this->processMail($request->get('msg_private_project'));
    }

    /**
     * Returns the type of the error to manage
     *
     * @return String
     */
    public function getType()
    {
        return 'private_project';
    }

    public function returnBuildInterfaceParam()
    {
    }

    /**
     * @return \CSRFSynchronizerToken
     */
    private function getToken()
    {
        return new \CSRFSynchronizerToken("/join-private-project-mail/");
    }
}
