<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController
{
    #[Route('/sitemap.xml', name: 'sitemap', methods: ['GET'])]
    public function sitemap(): Response
    {
        $urls = [
            'https://julienautomobiles.fr/',
            'https://julienautomobiles.fr/annonces',
            'https://julienautomobiles.fr/services',
            'https://julienautomobiles.fr/contact',
            'https://julienautomobiles.fr/mentions-legales',
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url, ENT_XML1) . '</loc>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return new Response(
            $xml,
            Response::HTTP_OK,
            ['Content-Type' => 'application/xml']
        );
    }
}
