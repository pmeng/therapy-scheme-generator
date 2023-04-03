<?php

namespace App\Service;

use App\Repository\Therapy\LabelRepository;

class LabelService
{

  private $labelRepository;

  public function __construct(LabelRepository $labelRepository)
  {
    $this->labelRepository = $labelRepository;
  }

  public function getLabelsByIds($labelsIDs)
  {
    $selectedLabels = [];
    foreach ($labelsIDs as $labelID) {
      $selectedLabels[] = $this->labelRepository->find($labelID);
    }
    return $selectedLabels;
  }
}
