<?php

namespace App\Models;
use App\Models\Connexion;
use PDO;

class Project{

    private $conn;
    private $id;
    private $title;
    private $content;
    private $thumbnail;
    private $project_date;
    private $last_modification_date;
    private $viewcount;
    private $is_featured;
    private $id_category;
    private $status;
    private $author;
    private $tags;

    public function __construct() {
        $this->conn = Connexion::getInstance()->getConn();
    }
    public function getTags(): ?array {
        return $this->tags;
    }
    public function setTags(?array $tags): Project {
        $this->tags = $tags;
        return $this;
    }

    public function getId() : ?int{
        return $this->id;
    }

    public function setId( $id ) : Project{
        $this->id = $id;
        return $this;
    }
    public function getTitle() : ?string{
        return $this->title;
    }
    public function setTitle( $title ) : Project{
        $this->title = $title;
        return $this;
    }
    public function getContent() : ?string{
        return $this->content;
    }
    public function setContent( $content ) : Project{
        $this->content = $content;
        return $this;
    }
    public function getThumbnail() : ?string{
        return $this->thumbnail;
    }
    public function setThumbnail( $thumbnail ) : Project{
        $this->thumbnail = $thumbnail;
        return $this;
    }
    public function getProjectDate() : ?string{
        return $this->project_date;
    }
    public function setProjectDate(string $project_date ) : Project{
        $this->project_date = $project_date;
        return $this;
    }
    public function getLastModificationDate() : ?string{
        return $this->last_modification_date;
    }
    public function setLastModificationDate(string $last_modified_date ) : Project{
        $this->last_modification_date = $last_modified_date;
        return $this;
    }
    public function getViewCount() : ?int{
        return $this->viewcount;
    }
    public function setViewCount(int $viewCount ) : Project{
        $this->viewcount = $viewCount;
        return $this;
    }
    public function getIsFeatured() : ?bool{
        return $this->is_featured;
    }
    public function setIsFeatured(bool $is_featured ) : Project{
        $this->is_featured = $is_featured;
        return $this;
    }
    public function getCategory() : ?Int{
        return $this->id_category;
    }
    public function setCategory(Int $id_category ) : Project{
        $this->id_category = $id_category;
        return $this;
    }
    public function getStatus() : ?int{
        return $this->status;
    }
    public function setStatus(int $status ) : Project{
        $this->status = $status;
        return $this;
    }
    public function getAuthor(): ?array {
        return $this->author;
    }

    public function setAuthor(?array $author): Project {
        $this->author = $author;
        return $this;
    }

    public static function hydrate($data){
        $project = new Project();

        $project
            ->setId($data["id"] ?? null)
            ->setTitle($data["title"] ?? null)
            ->setContent($data["content"] ?? null)
            ->setThumbnail($data["thumbnail"] ?? null)
            ->setProjectDate($data["project_date"] ?? null)
            ->setLastModificationDate($data["last_modification_date"] ?? null)
            ->setViewCount($data["viewcount"] ?? 0)
            ->setIsFeatured($data["is_featured"] ?? false)
            ->setCategory($data["id_category"] ?? null)
            ->setStatus($data["status"] ?? null);

        return $project;
    }


    public function find($id) {
        try {
            $stmt = $this->conn->prepare('
                SELECT
                    p.*,
                    u.id as author_id,
                    u.userName as author_username,
                    u.firstName as author_firstname,
                    u.lastName as author_lastname,
                    u.profile_picture as author_profile_picture,
                    u.email as author_email,
                    GROUP_CONCAT(t.id) as tag_ids,
                    GROUP_CONCAT(t.tag_name) as tag_names
                FROM project p
                LEFT JOIN project_user pu ON p.id = pu.id_project
                LEFT JOIN user u ON pu.id_user = u.id
                LEFT JOIN project_tags pt ON p.id = pt.id_project
                LEFT JOIN tag t ON pt.id_tag = t.id
                WHERE p.id = ?
                GROUP BY p.id
            ');
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                // Séparer les données du projet et de l'auteur
                $projectData = array_filter($data, function($key) {
                    return !str_starts_with($key, 'author_');
                }, ARRAY_FILTER_USE_KEY);

                // Préparer les données de l'auteur
                $authorData = null;
                if ($data['author_id']) {
                    $authorData = [
                        'id' => $data['author_id'],
                        'userName' => $data['author_username'],
                        'firstName' => $data['author_firstname'],
                        'lastName' => $data['author_lastname'],
                        'profile_picture' => $data['author_profile_picture'],
                        'email' => $data['author_email']
                    ];
                }
                // Préparer les données des tags
                $tags = null;
                if ($data['tag_ids'] && $data['tag_names']) {
                    $tagIds = explode(',', $data['tag_ids']);
                    $tagNames = explode(',', $data['tag_names']);
                    $tags = array_map(function($id, $name) {
                        return ['id' => $id, 'name' => $name];
                    }, $tagIds, $tagNames);
                }

                $project = self::hydrate($projectData);
                $project->setAuthor($authorData);
                $project->setTags($tags);

                return $project;
            }
            return null;
        } catch (\PDOException $e) {
            error_log("Erreur dans find(): " . $e->getMessage());
            throw new \Exception("Erreur lors de la récupération du projet: " . $e->getMessage());
        }
    }

    public function getAll() {
        try {
            $stmt = $this->conn->query('
                SELECT
                    p.*,
                    u.id as author_id,
                    u.userName as author_username,
                    u.firstName as author_firstname,
                    u.lastName as author_lastname,
                    u.profile_picture as author_profile_picture,
                    u.email as author_email,
                    GROUP_CONCAT(t.id) as tag_ids,
                    GROUP_CONCAT(t.tag_name) as tag_names
                FROM project p
                LEFT JOIN project_user pu ON p.id = pu.id_project
                LEFT JOIN user u ON pu.id_user = u.id
                LEFT JOIN project_tags pt ON p.id = pt.id_project
                LEFT JOIN tag t ON pt.id_tag = t.id
                GROUP BY p.id
                ORDER BY p.project_date DESC
            ');

            $projects = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $projectData = array_filter($data, function($key) {
                    return !str_starts_with($key, 'author_');
                }, ARRAY_FILTER_USE_KEY);

                $authorData = null;
                if ($data['author_id']) {
                    $authorData = [
                        'id' => $data['author_id'],
                        'userName' => $data['author_username'],
                        'firstName' => $data['author_firstname'],
                        'lastName' => $data['author_lastname'],
                        'profile_picture' => $data['author_profile_picture'],
                        'email' => $data['author_email']
                    ];
                }
                // Traitement des tags
                $tags = null;
                if ($data['tag_ids'] && $data['tag_names']) {
                    $tagIds = explode(',', $data['tag_ids']);
                    $tagNames = explode(',', $data['tag_names']);
                    $tags = array_map(function($id, $name) {
                        return ['id' => $id, 'name' => $name];
                    }, $tagIds, $tagNames);
                }

                $project = self::hydrate($projectData);
                $project->setAuthor($authorData);
                $project->setTags($tags);
                $projects[] = $project;
            }

            return $projects;
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des projets: " . $e->getMessage());
        }
    }
    public function getAllAsArray() {
        try {
            $projects = $this->getAll();
            return array_map(function($project) {
                return $project->toArray();
            }, $projects);
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des projets: " . $e->getMessage());
        }
    }

    public function createWithAuthor($data, $userId) {
        try {
            $this->conn->beginTransaction();

            // Insérer d'abord le projet
            $stmt = $this->conn->prepare('
                INSERT INTO project (
                    title, content, thumbnail, project_date,
                    last_modification_date, viewcount, is_featured,
                    id_category, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');

            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['thumbnail'],
                $data['project_date'],
                $data['last_modification_date'],
                $data['viewcount'],
                $data['is_featured'],
                $data['id_category'],
                $data['status']
            ]);

            $projectId = $this->conn->lastInsertId();

            // Créer ensuite la liaison avec l'auteur
            $stmt = $this->conn->prepare('
                INSERT INTO project_user (id_project, id_user)
                VALUES (?, ?)
            ');

            $stmt->execute([$projectId, $userId]);

            $this->conn->commit();
            return $projectId;

        } catch (\PDOException $e) {
            $this->conn->rollBack();
            throw new \Exception("Erreur lors de la création du projet: " . $e->getMessage());
        }
    }

    public function toArray(): array {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'thumbnail' => $this->thumbnail,
            'project_date' => $this->project_date,
            'last_modification_date' => $this->last_modification_date,
            'viewcount' => $this->viewcount,
            'is_featured' => $this->is_featured,
            'id_category' => $this->id_category,
            'status' => $this->status
        ];

        if ($this->author) {
            $data['author'] = $this->author;
        }

        if ($this->tags) {
            $data['tags'] = $this->tags;
        }

        return $data;
    }

    public function getProjectsByTag($tagId) {
        try {
            $stmt = $this->conn->prepare('
                SELECT DISTINCT
                    p.*,
                    u.id as author_id,
                    u.userName as author_username,
                    u.firstName as author_firstname,
                    u.lastName as author_lastname,
                    u.profile_picture as author_profile_picture,
                    u.email as author_email,
                    GROUP_CONCAT(t.id) as tag_ids,
                    GROUP_CONCAT(t.tag_name) as tag_names
                FROM project p
                LEFT JOIN project_user pu ON p.id = pu.id_project
                LEFT JOIN user u ON pu.id_user = u.id
                LEFT JOIN project_tags pt ON p.id = pt.id_project
                LEFT JOIN tag t ON pt.id_tag = t.id
                WHERE EXISTS (
                    SELECT 1
                    FROM project_tags pt2
                    WHERE pt2.id_project = p.id
                    AND pt2.id_tag = ?
                )
                GROUP BY p.id
                ORDER BY p.project_date DESC
            ');

            $stmt->execute([$tagId]);

            $projects = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                error_log("Projet trouve: " . json_encode($data));
                $projectData = array_filter($data, function($key) {
                    return !str_starts_with($key, 'author_');
                }, ARRAY_FILTER_USE_KEY);

                $authorData = null;
                if ($data['author_id']) {
                    $authorData = [
                        'id' => $data['author_id'],
                        'userName' => $data['author_username'],
                        'firstName' => $data['author_firstname'],
                        'lastName' => $data['author_lastname'],
                        'profile_picture' => $data['author_profile_picture'],
                        'email' => $data['author_email']
                    ];
                }
                // Traitement des tags
                $tags = null;
                if ($data['tag_ids'] && $data['tag_names']) {
                    $tagIds = explode(',', $data['tag_ids']);
                    $tagNames = explode(',', $data['tag_names']);
                    $tags = array_map(function($id, $name) {
                        return ['id' => $id, 'name' => $name];
                    }, $tagIds, $tagNames);
                }

                $project = self::hydrate($projectData);
                $project->setAuthor($authorData);
                $project->setTags($tags);
                $projects[] = $project;
            }
            error_log("Nombre total de projets trouves: " . count($projects));
            return $projects;
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des projets par tag: " . $e->getMessage());
        }
    }

    public function update($data) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare('
                UPDATE project
                SET title = ?,
                    content = ?,
                    thumbnail = ?,
                    last_modification_date = NOW(),
                    id_category = ?,
                    status = ?
                WHERE id = ?
            ');

            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['thumbnail'],
                $data['id_category'],
                $data['status'],
                $data['id']
            ]);

            // Update tags if provided
            if (isset($data['tags'])) {
                // First remove all existing tags
                $stmt = $this->conn->prepare('DELETE FROM project_tags WHERE id_project = ?');
                $stmt->execute([$data['id']]);

                // Then add new tags
                $stmt = $this->conn->prepare('INSERT INTO project_tags (id_project, id_tag) VALUES (?, ?)');
                foreach ($data['tags'] as $tag) {
                    $stmt->execute([$data['id'], $tag['id']]);
                }
            }

            $this->conn->commit();
            return true;

        } catch (\PDOException $e) {
            $this->conn->rollBack();
            throw new \Exception("Erreur lors de la mise à jour du projet: " . $e->getMessage());
        }
    }


}
