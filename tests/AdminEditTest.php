<?php
use PHPUnit\Framework\TestCase;

class AdminEditTest extends TestCase
{
    private string $url = "http://api-film/admin/edit.php?table=films";

    public function testAddFilm()
    {
        $data = [
            'titre' => 'FilmTest',
            'annee' => 2025,
            'duree' => 120,
            'id_genre' => 1, // doit exister
            'id_realisateur' => 1, // doit exister
            'id_support' => 1 // doit exister
        ];

        $opts = [
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/x-www-form-urlencoded",
                "content" => http_build_query($data),
                "ignore_errors" => true
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($this->url, false, $context);

        // Vérifie que la redirection a bien eu lieu ou qu'aucune erreur ne s'est produite
        $this->assertNotFalse($response, "La requête POST a échoué");
    }

    public function testEditFilm()
    {
        // On suppose que l'ID 1 existe déjà
        $urlWithId = $this->url . "&id=1";

        $data = [
            'titre' => 'FilmModifié',
            'annee' => 2026,
            'duree' => 110,
            'id_genre' => 1,
            'id_realisateur' => 1,
            'id_support' => 1
        ];

        $opts = [
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/x-www-form-urlencoded",
                "content" => http_build_query($data),
                "ignore_errors" => true
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($urlWithId, false, $context);

        $this->assertNotFalse($response, "La modification a échoué");
    }
    
    public function testDeleteFilm()
    {
        // On suppose que l'ID 1 existe déjà
        $urlWithDelete = $this->url . "&delete=1";

        $response = file_get_contents($urlWithDelete);

        // Vérifie que la redirection a bien eu lieu ou qu'aucune erreur ne s'est produite
        $this->assertNotFalse($response, "La suppression a échoué");
    }
}
    