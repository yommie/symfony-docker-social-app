<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * ApiResponse constructor.
     *
     * @param bool      Status
     * @param string    Message
     * @param int       Status Code
     * @param array     Data
     * @param array     Errors
     * @param array     Headers
     * @param bool      If data is already a Json String
     */
    public function __construct(
        bool $status,
        string $message,
        int $code = 200,
        array $data = [],
        array $errors = [],
        array $headers = [],
        bool $json = false
    ) {
        parent::__construct(
            $this->format($status, $message, $code, $data, $errors),
            $code,
            $headers,
            $json
        );
    }





    /**
     * Format the API response.
     *
     * @param bool      Status
     * @param string    Message
     * @param int       Code
     * @param array     Data
     * @param array     Errors
     *
     * @return array
     */
    public static function format(
        bool $status,
        string $message,
        int $code = 500,
        array $data = [],
        array $errors = []
    ) {
        $response = [
            "status"    => $status,
            'code'      => $code,
            'message'   => $message,
            'data'      => $data,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
