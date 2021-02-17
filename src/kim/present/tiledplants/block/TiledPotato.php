<?php
declare(strict_types=1);

namespace kim\present\tiledplants\block;

use kim\present\tiledplants\data\PlantData;
use kim\present\tiledplants\traits\TiledCropsTrait;
use pocketmine\block\Potato;

final class TiledPotato extends Potato implements ITiledPlant{
    use TiledCropsTrait;

    public function getPlantData() : PlantData{
        return PlantData::POTATO();
    }
}