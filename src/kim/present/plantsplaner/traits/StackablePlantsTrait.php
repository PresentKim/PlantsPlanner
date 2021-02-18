<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\data\StackablePlantsData;
use kim\present\plantsplaner\tile\Plants;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\math\Facing;

/**
 * This trait provides a implementation for stackable `IPlants` to reduce boilerplate.
 *
 * @see IPlants
 */
trait StackablePlantsTrait{
    use PlantsTrait;

    /** @inheritDoc */
    public function growPlants() : void{
        /** @var Block|IPlants $this */
        if($this->canGrow()){
            $world = $this->pos->getWorld();
            for($y = 1; $y < $this->getMaxGrowth(); ++$y){
                $vec = $this->pos->add(0, $y, 0);
                if(!$world->isInWorld($vec->x, $vec->y, $vec->z))
                    break;

                $block = $world->getBlock($vec);
                if($block->isSameType($this))
                    continue;

                if($block->getId() === BlockLegacyIds::AIR){
                    $ev = new BlockGrowEvent($block, clone $this);
                    $ev->call();
                    if(!$ev->isCancelled()){
                        $pos = $block->getPos();
                        $world = $pos->getWorld();
                        $world->setBlock($pos, $ev->getNewState());
                    }
                    break;
                }else{
                    break;
                }
            }
        }
    }

    /** @inheritDoc */
    public function canGrow() : bool{
        if($this->getSide(Facing::DOWN)->isSameType($this))
            return false;

        $world = $this->pos->getWorld();
        for($y = 1; $y < $this->getMaxGrowth(); ++$y){
            $vec = $this->pos->add(0, $y, 0);
            if(!$world->isInWorld($vec->x, $vec->y, $vec->z))
                break;

            $block = $world->getBlock($vec);
            if($block->isSameType($this))
                continue;

            if($block->getId() === BlockLegacyIds::AIR){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    /** @see StackablePlantsData::getMaxGrowth() */
    public function getMaxGrowth() : int{
        return $this->getPlantsData()->getMaxGrowth();
    }

    /**
     * @override to register scheduling when near block changed.
     * Since growth is handled at bottom, it searches for bottom and then adds that to the scheduling.
     */
    public function onNearbyBlockChange() : void{
        /**
         * @noinspection PhpUndefinedClassInspection
         * @see Block::onNearbyBlockChange()
         */
        parent::onNearbyBlockChange();

        $floor = $this;
        while(($down = $floor->getSide(Facing::DOWN))->isSameType($this)){
            $floor = $down;
        }

        $world = $this->pos->getWorld();
        $plantsTile = $world->getTile($floor->getPos());
        if($plantsTile instanceof Plants){
            $plantsTile->setLastTime(microtime(true));
        }
        $world->scheduleDelayedBlockUpdate($floor->getPos(), Plants::$updateDelay);
    }
}