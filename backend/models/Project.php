<?php

namespace models;
use models\Connexion;

class Project{
    
    private $id;
    private $title;
    private $content;
    private $thumbnail;
    private $project_date;
    private $last_modification_date;
    private $viewCount;
    private $is_featured;
    private $id_category;
    private $status;

    public function __construct() {
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
    }
    public function getViewCount() : ?int{
        return $this->view_count;
    }
    public function setViewCount(int $view_count ) : Project{
        $this->view_count = $view_count;
        return $this;
    }
    public function getIsFeatured() : ?bool{
        return $this->is_featured;
    }
    public function setIsFeatured(bool $is_featured ) : Project{
        $this->is_featured = $is_featured;
        return $this;
    }
    public function getCategory() : ?Category{
        return $this->category;
    }
    public function setCategory(Category $category ) : Project{
        $this->category = $category;
        return $this;
    }
    public function getStatus() : ?int{
        return $this->status;
    }
    public function setStatus(int $status ) : Project{
        $this->status = $status;
        return $this;
    }

    public static function hydrate($data){
        $project = new Project()
        ->setId($data["id"])
        ->setTitle($data["title"])
        ->setContent($data["content"])
        ->setThumbnail($data["thumbnail"])
        ->setProjectDate($data["project_date"])
        ->setLastModificationDate($data["last_modification_date"])
        ->setViewCount($data["view_count"])
        ->setIsFeatured($data["is_featured"])
        ->setCategory($data["category"])
        ->setStatus($data["status"]);     
        return $project;
    }



    public function test(){
        $conn = Connexion::getInstance()->getConn();

    }



    
}