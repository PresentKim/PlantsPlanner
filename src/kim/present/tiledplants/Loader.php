<?php
declare(strict_types=1);

namespace kim\present\tiledplants;

use kim\present\tiledplants\block\TiledBeetroot;
use kim\present\tiledplants\block\TiledCactus;
use kim\present\tiledplants\block\TiledCarrot;
use kim\present\tiledplants\block\TiledMelonStem;
use kim\present\tiledplants\block\TiledPotato;
use kim\present\tiledplants\block\TiledPumpkinStem;
use kim\present\tiledplants\block\TiledSugarcane;
use kim\present\tiledplants\block\TiledWheat;
use kim\present\tiledplants\data\BearablePlantData;
use kim\present\tiledplants\data\PlantData;
use kim\present\tiledplants\data\StackablePlantData;
use kim\present\tiledplants\tile\Plants;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\TileFactory;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\RegistryTrait;
use pocketmine\utils\SingletonTrait;

/**
 * @method static PlantData WHEAT()
 * @method static PlantData POTATO()
 * @method static PlantData CARROT()
 * @method static PlantData BEETROOT()
 *
 * @method static BearablePlantData MELON_STEM()
 * @method static BearablePlantData PUMPKIN_STEM()
 *
 * @method static StackablePlantData SUGARCANE()
 * @method static StackablePlantData CACTUS()
 */
final class Loader extends PluginBase{
    use SingletonTrait, RegistryTrait;

    public static int $updateDelay = 60 * 20;

    protected function onLoad() : void{
        self::$instance = $this;

        //Register Plants tile
        TileFactory::getInstance()->register(Plants::class, ["Plants", "presentkim:plants"]);

        //Resiter plant blocks
        $factory = BlockFactory::getInstance();
        $factory->register(new TiledWheat(new BlockIdentifier(BlockLegacyIds::WHEAT_BLOCK, 0, ItemIds::WHEAT_SEEDS, Plants::class), "Wheat Block"), true);
        $factory->register(new TiledPotato(new BlockIdentifier(BlockLegacyIds::POTATO_BLOCK, 0, ItemIds::POTATO, Plants::class), "Potato Block"), true);
        $factory->register(new TiledCarrot(new BlockIdentifier(BlockLegacyIds::CARROT_BLOCK, 0, ItemIds::CARROT, Plants::class), "Carrot Block"), true);
        $factory->register(new TiledBeetroot(new BlockIdentifier(BlockLegacyIds::BEETROOT_BLOCK, 0, ItemIds::BEETROOT, Plants::class), "Beetroot Block"), true);
        $factory->register(new TiledMelonStem(new BlockIdentifier(BlockLegacyIds::MELON_STEM, 0, ItemIds::MELON_SEEDS, Plants::class), "Melon Stem"), true);
        $factory->register(new TiledPumpkinStem(new BlockIdentifier(BlockLegacyIds::PUMPKIN_STEM, 0, ItemIds::PUMPKIN_SEEDS, Plants::class), "Pumpkin Stem"), true);
        $factory->register(new TiledSugarcane(new BlockIdentifier(BlockLegacyIds::SUGARCANE_BLOCK, 0, ItemIds::SUGARCANE, Plants::class), "Sugarcane"), true);
        $factory->register(new TiledCactus(new BlockIdentifier(BlockLegacyIds::CACTUS, 0, ItemIds::CACTUS, Plants::class), "Cactus"), true);
    }

    private function getConfigFloat(string $k, float $default) : float{
        return (float) $this->getConfig()->getNested($k, $default);
    }

    protected static function setup() : void{
        $config = self::getInstance();
        self::$updateDelay = max(1, (int) ($config->getConfigFloat("global.update-delay", 60) * 20));
        self::_registryRegister("wheat", new PlantData($config->getConfigFloat("wheat.grow-seconds", 60.0)));
        self::_registryRegister("potato", new PlantData($config->getConfigFloat("potato.grow-seconds", 60.0)));
        self::_registryRegister("carrot", new PlantData($config->getConfigFloat("carrot.grow-seconds", 60.0)));
        self::_registryRegister("beetroot", new PlantData($config->getConfigFloat("beetroot.grow-seconds", 60.0)));
        self::_registryRegister("melon_stem", new BearablePlantData(
            $config->getConfigFloat("mellon_stem.grow-seconds", 30.0),
            $config->getConfigFloat("mellon_stem.bear-seconds", 300.0)
        ));
        self::_registryRegister("pumpkin_stem", new BearablePlantData(
            $config->getConfigFloat("pumpkin_stem.grow-seconds", 30.0),
            $config->getConfigFloat("pumpkin_stem.bear-seconds", 300.0)
        ));
        self::_registryRegister("sugarcane", new StackablePlantData(
            $config->getConfigFloat("sugarcane.grow-seconds", 60.0),
            (int) $config->getConfigFloat("sugarcane.max-height", 3)
        ));
        self::_registryRegister("cactus", new StackablePlantData(
            $config->getConfigFloat("cactus.grow-seconds", 60.0),
            (int) $config->getConfigFloat("cactus.max-height", 3)
        ));
    }
}