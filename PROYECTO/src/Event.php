<?php
// este un Modelo simple para eventos
require_once __DIR__ . '/Database.php';
class Event {
    private $pdo;
    public function __construct(){
        $this->pdo = (new Database())->pdo();
    }

    public function create($data){
        $stmt = $this->pdo->prepare('INSERT INTO events (organizer_id,venue_id,title,description,start_datetime,end_datetime,capacity,price,status) VALUES (:org,:venue,:title,:desc,:start,:end,:cap,:price,:status)');
        $stmt->execute([
            ':org'=>$data['organizer_id'],
            ':venue'=>$data['venue_id']?:null,
            ':title'=>$data['title'],
            ':desc'=>$data['description'],
            ':start'=>$data['start_datetime'],
            ':end'=>$data['end_datetime']?:null,
            ':cap'=>$data['capacity']?:null,
            ':price'=>$data['price']?:0.00,
            ':status'=>$data['status']?:'draft'
        ]);
        return $this->pdo->lastInsertId();
    }

    public function allPublished(){
        $stmt = $this->pdo->query("SELECT e.*, o.organization_name, v.name as venue_name FROM events e JOIN organizers o ON e.organizer_id=o.id LEFT JOIN venues v ON e.venue_id=v.id WHERE e.status='published' ORDER BY e.start_datetime");
        return $stmt->fetchAll();
    }

    public function find($id){
        $stmt = $this->pdo->prepare('SELECT * FROM events WHERE id=:id LIMIT 1');
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }
}
?>
