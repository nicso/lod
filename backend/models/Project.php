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
        if (!$this->conn) {
            error_log("Erreur : Connexion non établie dans le constructeur de Project");
            throw new \Exception("La connexion à la base de données n'a pas pu être établie");
        }
        error_log("Connexion établie dans le constructeur de Project");
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
    public function getConnection() {
        return $this->conn;
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
    public function createWithAuthor($data, $userId) {
        error_log("=== DÉBUT DE L'INSERTION DU PROJET ===");

        try {
            error_log("Data reçue : " . print_r($data, true));
            error_log("UserID : " . $userId);

            // Vérifier l'état de la connexion
            if (!$this->conn->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
                throw new \Exception("La connexion à la base de données est inactive");
            }
            error_log("État de la transaction : " . ($this->conn->inTransaction() ? 'active' : 'inactive'));
            error_log("Auto-commit : " . ($this->conn->getAttribute(PDO::ATTR_AUTOCOMMIT) ? 'on' : 'off'));

            $this->conn->beginTransaction();
            error_log("Transaction démarrée");


            // Construire la requête SQL complète avec les valeurs
            $query = "
                INSERT INTO project (
                    title,
                    content,
                    thumbnail,
                    project_date,
                    last_modification_date,
                    viewcount,
                    is_featured,
                    id_category,
                    status
                ) VALUES (
                    :title,
                    :content,
                    :thumbnail,
                    :project_date,
                    :last_modification_date,
                    :viewcount,
                    :is_featured,
                    :id_category,
                    :status
                )
            ";

            $stmt = $this->conn->prepare($query);


            // Préparation des paramètres
            $params = [
                ':title' => $data['title'],
                ':content' => $data['content'],
                ':thumbnail' => $data['thumbnail'] ?? '',
                ':project_date' => $data['project_date'],
                ':last_modification_date' => $data['last_modification_date'],
                ':viewcount' => $data['viewcount'] ?? 0,
                ':is_featured' => $data['is_featured'] ? 1 : 0,
                ':id_category' => $data['id_category'],
                ':status' => $data['status'] ?? 0
            ];

            error_log("==== REQUÊTE D'INSERTION ====");
            error_log("Paramètres : " . print_r($params, true));

            // Log de la requête complète
            $logQuery = $query;
            foreach ($params as $key => $value) {
                $logQuery = str_replace($key, is_string($value) ? "'$value'" : $value, $logQuery);
            }
            error_log("Requête SQL complète : " . $logQuery);

            // Exécution de la requête
            $result = $stmt->execute($params);
            error_log("Résultat de l'exécution : " . ($result ? "succès" : "échec"));

            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Erreur PDO : " . print_r($errorInfo, true));
                throw new \Exception("Échec de l'insertion du projet: " . $errorInfo[2]);
            }

            $projectId = $this->conn->lastInsertId();
            error_log("ID du projet créé : " . $projectId);

            // Vérification immédiate de l'insertion
            $verifyStmt = $this->conn->prepare("SELECT id FROM project WHERE id = ?");
            $verifyStmt->execute([$projectId]);
            $verifyResult = $verifyStmt->fetch();
            error_log("Vérification de l'insertion : " . ($verifyResult ? "projet trouvé" : "projet non trouvé"));

            // Liaison avec l'auteur
            $userQuery = "INSERT INTO project_user (id_project, id_user) VALUES (:project_id, :user_id)";
            $userStmt = $this->conn->prepare($userQuery);
            $userResult = $userStmt->execute([
                ':project_id' => $projectId,
                ':user_id' => $userId
            ]);
            error_log("Résultat de la liaison utilisateur : " . ($userResult ? "succès" : "échec"));

            if (!$userResult) {
                $errorInfo = $userStmt->errorInfo();
                error_log("Erreur PDO liaison utilisateur : " . print_r($errorInfo, true));
                throw new \Exception("Échec de la liaison utilisateur-projet");
            }

            // Commit de la transaction
            $this->conn->commit();
            error_log("Transaction validée avec succès");

            // Vérification finale
            $finalVerifyStmt = $this->conn->prepare("
                SELECT p.*, pu.id_user
                FROM project p
                JOIN project_user pu ON p.id = pu.id_project
                WHERE p.id = ?
            ");
            $finalVerifyStmt->execute([$projectId]);
            $finalVerifyResult = $finalVerifyStmt->fetch();
            error_log("Vérification finale : " . print_r($finalVerifyResult, true));

            error_log("=== FIN DE L'INSERTION DU PROJET ===");
            return $projectId;

        } catch (\Exception $e) {
            error_log("Exception dans createWithAuthor : " . $e->getMessage());
            error_log("Trace : " . $e->getTraceAsString());
            try {
                $this->conn->rollBack();
                error_log("Transaction annulée");
            } catch (\Exception $rollbackException) {
                error_log("Erreur lors de l'annulation de la transaction : " . $rollbackException->getMessage());
            }
            throw $e;
        }
    }

    public function search(string $searchTerm = '', ?array $tags = null) {
        try {
            $query = '
                SELECT DISTINCT
                    p.*,
                    u.id as author_id,
                    u.userName as author_username,
                    u.firstName as author_firstname,
                    u.lastName as author_lastname,
                    u.profile_picture as author_profile_picture,
                    u.email as author_email,
                    GROUP_CONCAT(DISTINCT t.id) as tag_ids,
                    GROUP_CONCAT(DISTINCT t.tag_name) as tag_names
                FROM project p
                LEFT JOIN project_user pu ON p.id = pu.id_project
                LEFT JOIN user u ON pu.id_user = u.id
                LEFT JOIN project_tags pt ON p.id = pt.id_project
                LEFT JOIN tag t ON pt.id_tag = t.id
            ';

            $params = [];
            $conditions = [];

            // Condition pour la recherche textuelle
            if (!empty($searchTerm)) {
                $conditions[] = '(
                    p.title LIKE ?
                    OR p.content LIKE ?
                    OR u.userName LIKE ?
                    OR t.tag_name LIKE ?
                )';
                $searchPattern = "%{$searchTerm}%";
                $params = array_merge($params, array_fill(0, 4, $searchPattern));
            }

            // Condition pour la recherche par tags
            if (!empty($tags)) {
                $placeholders = str_repeat('?,', count($tags) - 1) . '?';
                $conditions[] = 'p.id IN (
                    SELECT pt2.id_project
                    FROM project_tags pt2
                    WHERE pt2.id_tag IN (' . $placeholders . ')
                    GROUP BY pt2.id_project
                    HAVING COUNT(DISTINCT pt2.id_tag) = ?
                )';
                $params = array_merge($params, $tags, [count($tags)]);
            }

            // Ajouter les conditions à la requête
            if (!empty($conditions)) {
                $query .= ' WHERE ' . implode(' AND ', $conditions);
            }

            $query .= ' GROUP BY p.id ORDER BY p.project_date DESC';

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);

            $projects = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Filtrer les données du projet
                $projectData = array_filter($data, function($key) {
                    return !str_starts_with($key, 'author_');
                }, ARRAY_FILTER_USE_KEY);

                // Traiter les données de l'auteur
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

                // Traiter les tags
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
            throw new \Exception("Erreur lors de la recherche des projets: " . $e->getMessage());
        }
    }

}
