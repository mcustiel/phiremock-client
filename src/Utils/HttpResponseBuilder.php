<?php
/**
 * This file is part of Phiremock.
 *
 * Phiremock is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Phiremock is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Phiremock.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mcustiel\Phiremock\Client\Utils;

use Mcustiel\Phiremock\Domain\Http\BinaryBody;
use Mcustiel\Phiremock\Domain\Http\Body;
use Mcustiel\Phiremock\Domain\Http\Header;
use Mcustiel\Phiremock\Domain\Http\HeaderName;
use Mcustiel\Phiremock\Domain\Http\HeadersCollection;
use Mcustiel\Phiremock\Domain\Http\HeaderValue;
use Mcustiel\Phiremock\Domain\Http\StatusCode;
use Mcustiel\Phiremock\Domain\HttpResponse;
use Mcustiel\Phiremock\Domain\Response;

class HttpResponseBuilder extends ResponseBuilder
{
    /** @var StatusCode */
    private $statusCode;

    /** @var Body */
    private $body;

    /** @var HeadersCollection */
    private $headers;

    public function __construct(StatusCode $statusCode)
    {
        $this->headers = new HeadersCollection();
        $this->statusCode = $statusCode;
        $this->body = Body::createEmpty();
    }

    public static function create(int $statusCode): HttpResponseBuilder
    {
        return new static(new StatusCode($statusCode));
    }

    public function andBody(string $body): HttpResponseBuilder
    {
        $this->body = new Body($body);

        return $this;
    }

    public function andBinaryBody(string $body): HttpResponseBuilder
    {
        $this->body = new BinaryBody($body);

        return $this;
    }

    public function andHeader(string $header, string $value): HttpResponseBuilder
    {
        $this->headers->setHeader(
            new Header(new HeaderName($header), new HeaderValue($value))
        );

        return $this;
    }

    /** @return Response|HttpResponse */
    public function build(): Response
    {
        $response = parent::build();

        return new HttpResponse(
            $this->statusCode,
            $this->body,
            $this->headers,
            $response->getDelayMillis(),
            $response->getNewScenarioState()
        );
    }
}
