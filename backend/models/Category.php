<?php

namespace models;
use PDOException;

class Category
{
	private int $id;
	private string $name;
	public function __construct(string $name)
	{
		$this->name = $name;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): Category
	{
		$this->id = $id;
		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): Category
	{
		$this->name = $name;
		return $this;
	}

	public static function toArray():array
	{
		$conn = Connexion::getInstance()->getConn();
		$stt = $conn->prepare("
			select id_category, category_name from category;
		");
		$stt->execute();
		return $stt->fetchAll();
	}

	public function save()
	{
		try {
			$conn = Connexion::getInstance()->getConn();
			$stt = $conn->prepare(
				"INSERT INTO category (category_name) VALUES (?)"
			);
			$stt->bindParam(1, $this->name);
			$stt->execute();
			$this->setId($conn->lastInsertId());
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public static function hydrate(array $params):Category
	{
		$category = new Category($params['category_name']);
		$category->setId($params['id_category']);
		return $category;
	}


}
