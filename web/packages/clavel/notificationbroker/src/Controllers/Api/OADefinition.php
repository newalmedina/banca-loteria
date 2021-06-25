<?php

/**
 * @OA\Tag(
 *     name="Notifications",
 *     description="Operations about notifications",
 * )
 */

/**
 * @OA\Schema(
 *   schema="Notification",
 *   required={"type", "receivers"},
 *   @OA\Property(
 *      property="type",
 *      type="string",
 *      enum={"email-001", "email-002", "sms-001", "sms-002"},
 *      description="We will use unique IDs to identify the notification types.
 *               We will use it to choose the right message",
 *      readOnly=true
 *   ),
 *   @OA\Property(
 *      property="forced",
 *      type="boolean",
 *      description="Optional. Default false. If forced is true, time frame restrictions
 *                  are ignored. If not exists or false, the notification is delayed and sended
 *                  in the allowed time frames",
 *      readOnly=true
 *   ),
 *   @OA\Property(
 *      property="receivers",
 *      type="array",
 *      minItems=1,
 *      uniqueItems=true,
 *      nullable=false,
 *      @OA\Items(
 *          allOf={
 *              @OA\Schema(
 *                  required={"to"},
 *                  @OA\Property(
 *                      property="to",
 *                      type="string",
 *                      readOnly=true
 *                  ),
 *                  @OA\Property(
 *                      property="uid",
 *                      type="string",
 *                      description="Optional. Default ''. Origin uid.",
 *                      readOnly=true
 *                  ),
 *                  @OA\Property(
 *                      property="subject",
 *                      type="string",
 *                      readOnly=true
 *                  ),
 *                  @OA\Property(
 *                      property="params",
 *                      type="array",
 *                      minItems=0,
 *                      uniqueItems=true,
 *                      nullable=true,
 *                      @OA\Items(
 *                          allOf={
 *                              @OA\Schema(
 *                                  @OA\Property(
 *                                      property="key",
 *                                      type="string",
 *                                      readOnly=true
 *                                  )
 *                              )
 *                          }
 *                      )
 *                  ),
 *                  @OA\Property(
 *                      property="certified",
 *                      type="boolean",
 *                      description="Optional. Default false. If certified is true, the sms is send
 *                              as certified. Be careful, the cost of this message is higher than the normal one.",
 *                      readOnly=true
 *                  ),
 *              )
 *          }
 *      )
 *    ),
 *    example={"type": "email-001", "receivers": {
 * {"to": "info@aduxia.com",
 * "subject": "Título del email a recibir(opcional)",
 * "uid": "49082340890820349203",
 * "params": {"name": "Jose Juan", "surname": "Calvo", "code": "000305850667"} }}
 * }
 * ),
 * @OA\Schema(
 *      schema="NotificationResponse",
 *      required={"type", "ids"},
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"email-001", "email-002", "sms-001", "sms-002"},
 *          description="We will use unique IDs to identify the notification types.
 *                   We will use it to choose the right message",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="ids",
 *          type="array",
 *          minItems=1,
 *          uniqueItems=true,
 *          nullable=false,
 *          @OA\Items(
 *              @OA\Property(
 *                  property="to",
 *                  type="string",
 *                  readOnly=true
 *              ),
 *              @OA\Property(
 *                  property="guid",
 *                  type="string",
 *                  readOnly=true
 *              ),
 *              example = { "to": "info@aduxia.com", "guid": "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"}
 *          )
 *      )
 *  )
 */
