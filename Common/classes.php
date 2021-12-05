<?php

Class Album{
    
    public $albumID;
    public $title;
    public $description;
    public $dateUpdated;
    public $ownerID;
    public $accessibilityCode;
    public $pictureCount;
    
    public function __construct(string $albumID, string $title, string $description, string $dateUpdated, string $ownerID, string $accessibilityCode) {
        $this-$this->albumID = $albumID;
        $this->title = $title;
        $this->description = $description;
        $this->dateUpdated = $dateUpdated;
        $this->ownerID = $ownerID;
        $this->accessibilityCode = $accessibilityCode;
    }
    
}

class Accessibility{
    public $accessibilityCode;
    public $description;
    
    public function __construct(string $accessibilityCode, string $description) {
        $this->accessibilityCode = $accessibilityCode;
        $this->description = $description;
    }
}

?>