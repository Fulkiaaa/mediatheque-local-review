<?php
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private $baseUrl = "http://api-film/api.php";

    public function testGetFilms()
    {
        // Test de récupération des films (seule fonctionnalité disponible)
        $response = file_get_contents($this->baseUrl);
        
        // Vérifier que la réponse est du JSON valide
        $this->assertJson($response, "La réponse n'est pas du JSON valide");
        
        $data = json_decode($response, true);
        $this->assertIsArray($data, "Les données décodées ne sont pas un tableau");
        
        // Vérifier la structure attendue
        $this->assertArrayHasKey('films', $data, "La clé 'films' n'existe pas");
        $this->assertIsArray($data['films'], "La valeur 'films' n'est pas un tableau");
        
        // Si il y a des films, vérifier la structure d'un film
        if (!empty($data['films'])) {
            $film = $data['films'][0];
            $this->assertArrayHasKey('film_id', $film);
            $this->assertArrayHasKey('titre', $film);
            $this->assertArrayHasKey('genre', $film);
            $this->assertArrayHasKey('acteurs', $film);
        }
    }

    public function testApiResponseTime()
    {
        $start = microtime(true);
        $response = file_get_contents($this->baseUrl);
        $end = microtime(true);
        
        $responseTime = $end - $start;
        $this->assertLessThan(2.0, $responseTime, "L'API met plus de 2 secondes à répondre");
        $this->assertNotFalse($response, "L'API n'a pas répondu");
    }

    public function testApiErrorHandling()
    {
        // Test avec une URL invalide pour vérifier la gestion d'erreur
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => 'invalid json{',
                'ignore_errors' => true
            ]
        ]);
        
        $response = file_get_contents($this->baseUrl, false, $context);
        
        // L'API devrait toujours retourner une réponse, même avec des données invalides
        $this->assertNotFalse($response, "L'API n'a pas géré correctement les erreurs");
    }
}

  