<?php

namespace app\modules\v1\admin\swagger;

/**
 * @SWG\Definition(
 *     definition = "GeneralError",
 *
 *     @SWG\Property(property="code",    type="integer", description="Error code"),
 *     @SWG\Property(property="message", type="string",  description="Readable error message"),
 *     @SWG\Property(property="short_message", type="string", description="Short version of the error message"),
 * )
 */

/**
 * @SWG\Definition(
 *     definition="UnprocessableError",
 *
 *     @SWG\Property(property="code",    type="integer", description="Error code"),
 *     @SWG\Property(property="message", type="string",  description="Readable error message"),
 *     @SWG\Property(property="short_message", type="string", description="Short version of the error message"),
 *     @SWG\Property(property="form_errors", type="object",   description="List of errors for each invalid attributes"),
 * )
 */

/**
 * @SWG\Definition(
 *     definition  = "HATEOAS",
 *     description = "Hypermedia links are used to navigate dynamically to the appropriate resource by traversing the hypermedia links.",
 *     
 *     @SWG\Property( property = "rel",  type = "string" ),
 *     @SWG\Property( property = "href", type = "string" ),
 *     @SWG\Property( property = "type", type = "string" ),
 * )
 */