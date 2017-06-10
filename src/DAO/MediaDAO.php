<?php
namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Media;
//use Doctrine\DBAL\Driver\SQLSrv\LastInsertId;

class MediaDAO extends DAO {
	// Add a new media
	
	public function addMedia(Media $media) {
		$mediaData = array(
				'file_name' => $media->getFileName(),
				'url_file'	=> $media->getUrlFile(),
				'post_id'	=> $media->getPostId()
		);
		
		$req = $this->getDb()->prepare('INSERT INTO medias(file_name, url_file, post_id) VALUES(:file_name, :url_file, :post_id)');
		$req->execute($mediaData);	
		
	}
	
	protected function buildDomainObject(array $row) {
		$media = new Media();
		$media->setId($row['id_media']);
		$media->setFileName($row['file_name']);
		$media->setUrlFile($row['url_file']);
		$media->setPostId($row['post_id']);
		
		return $media;
	}
	
	
}