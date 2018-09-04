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

namespace Tuleap\OpenIDConnectClient\Authentication\UserInfo;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class UserInfoResponseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @expectedException \Tuleap\OpenIDConnectClient\Authentication\UserInfo\NotSupportedContentTypeUserInfoResponseException
     */
    public function testContentTypeNotAnnouncedAsJSONIsRejected()
    {
        $http_response = \Mockery::mock(ResponseInterface::class);
        $http_response->shouldReceive('getHeaderLine')->andReturns('application/jwt');

        UserInfoResponse::buildFromHTTPResponse($http_response);
    }

    /**
     * @expectedException \Tuleap\OpenIDConnectClient\Authentication\UserInfo\IncorrectlyFormattedUserInfoResponseException
     */
    public function testNotValidJSONISRejected()
    {
        $http_response = \Mockery::mock(ResponseInterface::class);
        $http_response->shouldReceive('getHeaderLine')->andReturns('application/json');
        $http_response->shouldReceive('getBody')->andReturns('{NotJSONValid');

        UserInfoResponse::buildFromHTTPResponse($http_response);
    }

    public function testUserInfoResponseIsParsed()
    {
        $http_response = \Mockery::mock(ResponseInterface::class);
        $http_response->shouldReceive('getHeaderLine')->andReturns('application/json');
        $claims = [
            'sub'                => '248289761001',
            'name'               => 'Jane Doe',
            'given_name'         => 'Jane',
            'family_name'        => 'Doe',
            'preferred_username' => 'j.doe',
            'email'              => 'janedoe@example.com',
        ];
        $http_response->shouldReceive('getBody')->andReturns(json_encode($claims));

        $user_info_response = UserInfoResponse::buildFromHTTPResponse($http_response);
        $this->assertSame($claims, $user_info_response->getClaims());
    }

    public function testAnEmptyUserInfoResponseCanBeProvided()
    {
        $user_info_response = UserInfoResponse::buildEmptyUserInfoResponse();
        $this->assertEmpty($user_info_response->getClaims());
    }
}
