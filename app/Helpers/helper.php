<?php


use Illuminate\Http\JsonResponse;

   function apiResponse(bool $status, string $message, $data = null, int $statusCode = 200): JsonResponse
    {
        if ($status === false) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $data, // usually validation errors or message details
            ], $statusCode !== 200 ? $statusCode : 422); // override to 422 if status false and no custom code
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }


    function converteToSlug(string $string): string
    {
        // Convert to lowercase, replace spaces with hyphens, and remove special characters
        return strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($string)));
    }
    function convertToCamelCase(string $string): string
    {
       return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    function slugify($string)
    {
        // Convert to lowercase
        $slug = strtolower($string);

        // Remove special characters
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);

        // Replace multiple spaces or hyphens with a single hyphen
        $slug = preg_replace('/[\s-]+/', '-', $slug);

        // Trim hyphens from beginning and end
        $slug = trim($slug, '-');

        return $slug;
    }

   



    //required Class
    include_once __DIR__ . '/permissions.php';
    include_once __DIR__ . '/class/Question.php';
    include_once __DIR__ . '/class/QuestionParser.php';
    include_once __DIR__ . '/class/QuestionParserFromText.php';
    

?>