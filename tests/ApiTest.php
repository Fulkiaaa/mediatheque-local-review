<?php
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private $baseUrl = "http://api-film/api.php";

    public function testGetFilms()
    {
        $response = file_get_contents($this->baseUrl . "?table=films");
        $this->assertJson($response);
        $data = json_decode($response, true);
        $this->assertIsArray($data);
    }

    public function testInsertFilm()
    {
        $data = [
            'titre' => 'TestFilm',
            'annee' => 2025,
            'id_genre' => 1,
            'id_realisateur' => 1,
            'id_support' => 1
        ];
        $opts = [
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/x-www-form-urlencoded",
                "content" => http_build_query($data)
            ]
        ];
        $context = stream_context_create($opts);
        $result = file_get_contents($this->baseUrl . "?table=films", false, $context);
        $this->assertJson($result);
    }
}
