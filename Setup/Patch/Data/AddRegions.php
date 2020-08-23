<?php
/**
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Iftakharul Alam Bappa <info@shiftenter.dev> 
 */

declare(strict_types=1);

namespace Shiftenterdev\SwedenRegions\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddRegions implements DataPatchInterface, PatchRevertableInterface
{
    const COUNTRY_CODE = 'SE';

    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /**
         * Fill table directory/country_region
         * Fill table directory/country_region_name for sv_SE locale
         */
        $data = [
            [self::COUNTRY_CODE,'BL', 'Blekinge'],
            [self::COUNTRY_CODE,'DA', 'Dalarna'],
            [self::COUNTRY_CODE,'GO', 'Gotland'],
            [self::COUNTRY_CODE,'GA', 'Gävleborg'],
            [self::COUNTRY_CODE,'HA', 'Halland'],
            [self::COUNTRY_CODE,'JM', 'Jämtland'],
            [self::COUNTRY_CODE,'JO', 'Jönköping'],
            [self::COUNTRY_CODE,'KA', 'Kalmar'],
            [self::COUNTRY_CODE,'KR', 'Kronoberg'],
            [self::COUNTRY_CODE,'NO', 'Norrbotten'],
            [self::COUNTRY_CODE,'SK', 'Skåne'],
            [self::COUNTRY_CODE,'ST', 'Stockholm'],
            [self::COUNTRY_CODE,'SO', 'Södermanland'],
            [self::COUNTRY_CODE,'UP', 'Uppsala'],
            [self::COUNTRY_CODE,'VR', 'Värmland'],
            [self::COUNTRY_CODE,'VS', 'Västerbotten'],
            [self::COUNTRY_CODE,'VL', 'Västernorrland'],
            [self::COUNTRY_CODE,'VM', 'Västmanland'],
            [self::COUNTRY_CODE,'VG', 'Västra Götaland'],
            [self::COUNTRY_CODE,'OR', 'Örebro'],
            [self::COUNTRY_CODE,'OS', 'Östergötland'],
        ];

        foreach ($data as $row) {
            $bind = ['country_id' => $row[0], 'code' => $row[1], 'default_name' => $row[2]];
            $this->moduleDataSetup->getConnection()->insert(
                $this->moduleDataSetup->getTable('directory_country_region'),
                $bind
            );

            $regionId = $this->moduleDataSetup->getConnection()->lastInsertId(
                $this->moduleDataSetup->getTable('directory_country_region')
            );

            $bind = ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[2]];
            $this->moduleDataSetup->getConnection()->insert(
                $this->moduleDataSetup->getTable('directory_country_region_name'),
                $bind
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Revert patch
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $tableDirectoryCountryRegionName = $this->moduleDataSetup->getTable('directory_country_region_name');
        $tableDirectoryCountryRegion = $this->moduleDataSetup->getTable('directory_country_region');

        $where = [
            'region_id IN (SELECT region_id FROM ' . $tableDirectoryCountryRegion . ' WHERE country_id = ?)' => self::COUNTRY_CODE
        ];
        $this->moduleDataSetup->getConnection()->delete(
            $tableDirectoryCountryRegionName,
            $where
        );

        $where = ['country_id = ?' => self::COUNTRY_CODE];
        $this->moduleDataSetup->getConnection()->delete(
            $tableDirectoryCountryRegion,
            $where
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
