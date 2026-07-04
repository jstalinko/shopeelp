<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index(): Response
    {
        $templatesDirectory = resource_path('views/templates');
        
        if (!File::isDirectory($templatesDirectory)) {
            File::makeDirectory($templatesDirectory, 0755, true, true);
        }

        $files = File::files($templatesDirectory);
        $discovered = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $filepath = $file->getPathname();
            $filename = basename($filepath, '.blade.php');
            $templatePath = 'templates/' . $filename;
            
            $content = file_get_contents($filepath);
            $parsed = $this->parseTemplateHeader($content, $filename);

            // Sync with Database
            $dbTemplate = Template::firstOrCreate(
                ['template_path' => $templatePath],
                [
                    'name' => $parsed['name'],
                    'description' => $parsed['description'],
                    'image' => null,
                    'config' => json_encode([]),
                    'active' => true,
                ]
            );

            // Update database name/description if changed in files
            if ($dbTemplate->name !== $parsed['name'] || $dbTemplate->description !== $parsed['description']) {
                $dbTemplate->update([
                    'name' => $parsed['name'],
                    'description' => $parsed['description']
                ]);
            }

            $discovered[] = [
                'id' => $dbTemplate->id,
                'name' => $dbTemplate->name,
                'description' => $dbTemplate->description,
                'image' => $dbTemplate->image,
                'template_path' => $dbTemplate->template_path,
                'active' => (bool)$dbTemplate->active,
                'db_config' => json_decode($dbTemplate->config, true) ?: [],
                'config_fields' => $parsed['config_fields'],
                'author' => $parsed['author'],
                'version' => $parsed['version'],
                'created' => $parsed['created'],
                'updated' => $parsed['updated'],
            ];
        }

        return Inertia::render('Dashboard/Templates', [
            'templates' => $discovered,
        ]);
    }

    /**
     * Update the configuration of a template.
     */
    public function updateConfig(Request $request, Template $template): RedirectResponse
    {
        $validated = $request->validate([
            'config' => 'required|array',
            'active' => 'required|boolean',
        ]);

        $template->update([
            'config' => json_encode($validated['config']),
            'active' => $validated['active'],
        ]);

        return redirect()->back();
    }

    /**
     * Helper method to parse metadata from the file comments header.
     */
    private function parseTemplateHeader(string $content, string $defaultName): array
    {
        $data = [
            'name' => ucfirst(str_replace('-', ' ', $defaultName)),
            'description' => 'Custom campaign redirection landing page.',
            'author' => 'Unknown',
            'version' => '1.0.0',
            'created' => date('Y-m-d'),
            'updated' => date('Y-m-d'),
            'config_fields' => [],
        ];

        if (preg_match('/<!--\|(.*?)\-->/s', $content, $matches)) {
            $commentBody = $matches[1];

            if (preg_match('/\|\s*Name:\s*(.*?)\r?\n/i', $commentBody, $nameMatch)) {
                $data['name'] = trim($nameMatch[1]);
            }
            if (preg_match('/\|\s*Description:\s*(.*?)\r?\n/i', $commentBody, $descMatch)) {
                $data['description'] = trim($descMatch[1]);
            }
            if (preg_match('/\|\s*Author:\s*(.*?)\r?\n/i', $commentBody, $authorMatch)) {
                $data['author'] = trim($authorMatch[1]);
            }
            if (preg_match('/\|\s*Version:\s*(.*?)\r?\n/i', $commentBody, $verMatch)) {
                $data['version'] = trim($verMatch[1]);
            }
            if (preg_match('/\|\s*Created:\s*(.*?)\r?\n/i', $commentBody, $createdMatch)) {
                $data['created'] = trim($createdMatch[1]);
            }
            if (preg_match('/\|\s*Updated:\s*(.*?)\r?\n/i', $commentBody, $updatedMatch)) {
                $data['updated'] = trim($updatedMatch[1]);
            }

            // Parse fields
            if (preg_match('/\|-Configuration Forms:(.*?)$/s', $commentBody, $configSectionMatch)) {
                $lines = explode("\n", $configSectionMatch[1]);
                foreach ($lines as $line) {
                    // Match line format like: | cfg_$title (string, required)
                    if (preg_match('/\|\s*cfg_\$?(\w+)\s*\(([^,]+),\s*([^)]+)\)/', $line, $fieldMatch)) {
                        $data['config_fields'][] = [
                            'name' => trim($fieldMatch[1]),
                            'type' => trim($fieldMatch[2]),
                            'required' => trim($fieldMatch[3]) === 'required',
                        ];
                    }
                }
            }
        }

        return $data;
    }
}
