<?php 
namespace ShortlinkGenerator;


class ShortlinkModel
{
	public ?string      $created_at;
    public ?string      $updated_at;
    public ?string      $scan_code;
    public ?string      $shortlink_url = "https://livingmap.link";
    public ?string      $real_url;
    public ?string      $description_en;
    public ?string      $project;


    public function insertSql()
    {
        $sql = '
INSERT INTO "shortlink"."shortlinks" (
    scan_code, 
    shortlink_url, 
    real_url, 
    created_at, 
    updated_at, 
    description_en, 
    project
) 
VALUES (
    \'%s\', 
    \'%s\', 
    \'%s\', 
    \'%s\', 
    \'%s\', 
    \'%s\', 
    \'%s\'
);
';

        return sprintf($sql,
            $this->scan_code,
            $this->shortlink_url,
            $this->real_url,
            $this->created_at,
            $this->updated_at,
            $this->description_en,
            $this->project
        );


    }
}