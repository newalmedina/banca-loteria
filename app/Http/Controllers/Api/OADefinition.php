<?php

/**
 * @OA\Info(
 *      version="0.0.1",
 *      title="Clavel API",
 *      description="Clavel OpenApi description.  Developed by Aduxia
[http://www.aduxia.com](http://www.aduxia.com) based on
[http://swagger.io](http://swagger.io).",
 *      termsOfService="http://www.aduxia.com",
 *      @OA\Contact(
 *          email="info@aduxia.com"
 *      )
 * )
 */

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Operations about authentication",
 * )
 * @OA\Tag(
 *     name="User",
 *     description="Operations about user",
 * )
 */

/**
 * @OA\SecurityScheme(
 *     securityScheme="Authorization",
 *     type="http",
 *     in="header",
 *     name="Bearer",
 *     scheme="bearer"
 * )
 */

/**
 * @OA\Header(
 *      header="X-Authorization-Token",
 *      description="Updated authorization token",
 *      @OA\Schema(
 *          schema="AuthorizationToken",
 *          type="string",
 *      )
 * )
 */

/**
 * @OA\Header(
 *      header="Last-Modified",
 *      description="<day-name>, <day> <month> <year> <hour>:<minute>:<second> GMT",
 *      @OA\Schema(
 *          schema="Last-Modified",
 *          type="string",
 *      )
 * )
 */

/**
 * @OA\Schema(
 *   schema="Date",
 *   type="string",
 *   description="
The Date in ISO 8601 format.
YYYY-MM-DD",
 *   example="2018-10-16"
 * ),
 * @OA\Schema(
 *   schema="Time",
 *   type="string",
 *   description="
The Time in ISO 8601 format.
<user's local time>±<time representation of the user's timezone>
hh:mm:ss±hh:mm:ss",
 *   example="11:15:00+01:00"
 * ),
 * @OA\Schema(
 *   schema="DateTime",
 *   type="string",
 *   description="
The DateTime in ISO 8601 format.
<YYYY-MM-DD>T<user's local time>±<time representation of the user's timezone>
YYYY-MM-DDThh:mm:ss±hh:mm:ss",
 *   example="2018-10-17T11:15:00+01:00"
 * ),
 * @OA\Schema(
 *   schema="Error",
 *   required={"code","message"},
 *   @OA\Property(
 *      property="code",
 *      type="string"
 *   ),
 *   @OA\Property(
 *      property="message",
 *      type="string"
 *   ),
 * ),
 * @OA\Schema(
 *   schema="User",
 *   required={"id","name","surname","email"},
 *   @OA\Property(
 *      property="id",
 *      type="integer",
 *      format="int64",
 *      readOnly=true
 *   ),
 *   @OA\Property(
 *      property="name",
 *      type="string"
 *   ),
 *   @OA\Property(
 *      property="surname",
 *      type="string"
 *   ),
 *   @OA\Property(
 *      property="second_surname",
 *      type="string"
 *   ),
 *   @OA\Property(
 *      property="email",
 *      type="string",
 *      format="email",
 *      readOnly=true
 *   ),
 *   @OA\Property(
 *      property="province",
 *      type="string"
 *   ),
 *   @OA\Property(
 *      property="zip_code",
 *      type="string"
 *   )
 * ),
 * @OA\Schema(
 *   schema="AccessTime",
 *   required={"start","end"},
 *   @OA\Property(
 *      property="start",
 *      ref="#/components/schemas/DateTime",
 *      nullable=false
 *   ),
 *   @OA\Property(
 *      property="end",
 *      ref="#/components/schemas/DateTime",
 *      nullable=false
 *   ),
 * ),
 * @OA\Schema(
 *   schema="PaginationLinks",
 *   required={"first","last","prev","next"},
 *   @OA\Property(
 *     property="first",
 *     type="string",
 *     readOnly=true,
 *     description="Url of the first page",
 *     example="http://clavel.test/api/v1/notifications?page=1"
 *   ),
 *   @OA\Property(
 *     property="last",
 *     type="string",
 *     readOnly=true,
 *     description="Url of the last page",
 *     example="http://clavel.test/api/v1/notifications?page=10"
 *   ),
 *   @OA\Property(
 *     property="prev",
 *     type="string",
 *     readOnly=true,
 *     description="Url of the previous page",
 *     example=null
 *   ),
 *   @OA\Property(
 *     property="next",
 *     type="string",
 *     readOnly=true,
 *     description="Url of the next page",
 *     example="http://clavel.test/api/v1/notifications?page=2"
 *   )
 * ),
 * @OA\Schema(
 *   schema="PaginationMeta",
 *   required={"current_page","from","last_page","path","per_page","to","total"},
 *   @OA\Property(
 *     property="current_page",
 *     type="integer",
 *     format="int64",
 *     readOnly=true,
 *     description="Number or current page",
 *     example=1
 *   ),
 *   @OA\Property(
 *      property="from",
 *      type="integer",
 *      format="int64",
 *      readOnly=true,
 *     description="Number or from record",
 *     example=1
 *   ),
 *   @OA\Property(
 *      property="last_page",
 *      type="integer",
 *      format="int64",
 *      readOnly=true,
 *     description="Number or last page",
 *     example=10
 *   ),
 *   @OA\Property(
 *      property="path",
 *      type="string",
 *      readOnly=true,
 *     description="Path of the pagination",
 *     example="http://clavel.test/api/v1/notifications"
 *   ),
 *   @OA\Property(
 *      property="per_page",
 *      type="integer",
 *      format="int64",
 *      readOnly=true,
 *     description="Number or records per page",
 *     example=15
 *   ),
 *   @OA\Property(
 *      property="to",
 *      type="integer",
 *      format="int64",
 *      readOnly=true,
 *     description="Number or to record",
 *     example=15
 *   ),
 *   @OA\Property(
 *      property="total",
 *      type="integer",
 *      format="int64",
 *      readOnly=true,
 *     description="Total of records",
 *     example=150
 *   ),
 * ),
 */


/**
 * @OA\Response(
 *     response="Unauthorized",
 *     description="Authentication is not valid.",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/Error"),
 *         example={"code": "401", "message": "Unauthorized"}
 *     )
 * ),
 * @OA\Response(
 *     response="InternalServerError",
 *     description="Internal Server Error.",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/Error"),
 *         example={"code": "500", "message": "Internal Server Error."}
 *     )
 * ),
 * @OA\Response(
 *     response="Forbidden",
 *     description="Forbidden.",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/Error"),
 *         example={"code": "403", "message": "Forbidden"}
 *     )
 * ),
 * @OA\Response(
 *     response="NotFound",
 *     description="Resource not found",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/Error"),
 *         example={"code": "404", "message": "Not Found"}
 *     )
 * ),
 * @OA\Response(
 *     response="UnprocessableEntity",
 *     description="Input data is not valid. We will define a more appropiate schema model",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/Error"),
 *         example={"code": "422", "message": "Input data is not valid. We will define a more appropiate schema model"}
 *     )
 * ),
 * @OA\Response(
 *     response="RangeNotSatisfiable",
 *     description="Range Not Satisfiable",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/Error"),
 *         example={"code": "416", "message": "Range Not Satisfiable"}
 *     )
 * ),
 */
