<?php
declare(strict_types=1);

namespace App\Services;

class PhilippineLocationsService
{
  protected array $locations;

  public function __construct()
  {
    $json = file_get_contents(public_path('philippine_provinces_cities_municipalities_and_barangays_2019v2.json'));
    $this->locations = json_decode($json, true);
  }

  public function getRegions(): array
  {
    // Returns array of ['code' => ..., 'name' => ...]
    return collect($this->locations)
      ->map(fn($region, $code) => [
        'code' => $code,
        'name' => $region['region_name'],
      ])
      ->values()
      ->all();
  }

  public function getProvinces(string $regionCode): array
  {
    return isset($this->locations[$regionCode]['province_list'])
      ? collect($this->locations[$regionCode]['province_list'])
        ->mapWithKeys(fn($item, $key) => [$key => $key])
        ->all()
      : [];
  }

  public function getMunicipalities(string $regionCode, string $province): array
  {
    return isset($this->locations[$regionCode]['province_list'][$province]['municipality_list'])
      ? collect($this->locations[$regionCode]['province_list'][$province]['municipality_list'])
        ->mapWithKeys(fn($item, $key) => [$key => $key])
        ->all()
      : [];
  }

  public function getBarangays(string $regionCode, string $province, string $municipality): array
  {
    return $this->locations[$regionCode]['province_list'][$province]['municipality_list'][$municipality]['barangay_list'] ?? [];
  }
}