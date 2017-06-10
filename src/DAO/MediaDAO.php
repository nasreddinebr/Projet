<?php
namespace BlogEcrivain\DAO;

use BlogEcrivain\Domain\Media;

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
	
	// Delete a media for a post
	public function removeMediaByPsot($postId) {
		$req = $this->getDb()->prepare('DELETE FROM medias WHERE post_id=:post_id');
		$req->execute(array('post_id' => $postId));
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