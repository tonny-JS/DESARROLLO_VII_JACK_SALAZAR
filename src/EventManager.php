<?php
// Lógica extra para gestionar eventos
require_once __DIR__ . '/Event.php';
class EventManager {
    private $model;
    public function __construct(){ $this->model = new Event(); }

    public function createFromPost($post){
        // Simple sanitization, expand as needed
        $data = [
            'organizer_id' => intval($post['organizer_id'] ?? 0),
            'venue_id' => intval($post['venue_id'] ?? 0) ?: null,
            'title' => trim($post['title'] ?? ''),
            'description' => trim($post['description'] ?? ''),
            'start_datetime' => $post['start_datetime'] ?? '',
            'end_datetime' => $post['end_datetime'] ?? null,
            'capacity' => $post['capacity'] ? intval($post['capacity']) : null,
            'price' => isset($post['price']) ? floatval($post['price']) : 0.00,
            'status' => $post['status'] ?? 'draft'
        ];
        return $this->model->create($data);
    }
}
?>