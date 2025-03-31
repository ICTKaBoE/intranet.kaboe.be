<?php

namespace Database\Repository\Signage;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class PlaylistItem extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_signage_playlist_item", \Database\Object\Signage\PlaylistItem::class, guidField: false);
    }

    public function getByPlaylistId($playlistId)
    {
        $statement = $this->prepareSelect();
        $statement->where("playlistId", $playlistId);

        return $this->executeSelect($statement);
    }

    public function getByMediaId($mediaId)
    {
        $statement = $this->prepareSelect();
        $statement->where("mediaId", $mediaId);

        return $this->executeSelect($statement);
    }
}
