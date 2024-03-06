<?php

namespace App\Service;

use App\Repository\Therapy\LabelRepository;
use App\Repository\Therapy\StubRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class SchemeService
{

  private $labelRepository;
  private $stubRepository;
  private $translator;

  public function __construct(LabelRepository $labelRepository, StubRepository $stubRepository, TranslatorInterface $translator)
  {
    $this->labelRepository = $labelRepository;
    $this->stubRepository = $stubRepository;
    $this->translator = $translator;
  }

  public function generateTbody(
    $selectedLabels,
    $suppress,
    $currentComments,
    $notCheckedCheckboxes,
    $stubsOrder = [],
    $excerpt,
    $currentLanguage
  ): string {
    $newTbody = '';
    
    foreach ($stubsOrder as $order) {
        $labelID = $order[0][0];
        $stubIDs = $order[0][1];

        if (in_array($labelID, $selectedLabels)) {
            $index = array_search($labelID, $selectedLabels);
            unset($selectedLabels[$index]);

            $label = $this->labelRepository->find($labelID);
            $labelStubs = [];

            foreach ($stubIDs as $stubID) {
                $stub = $this->stubRepository->find($stubID);
                if ($stub) {
                    $labelStubs[] = $stub;
                }
            }

            $newTbody .= $this->generateLabelTbody($label, $labelStubs, $suppress, $currentComments, $notCheckedCheckboxes, $excerpt, $currentLanguage);
        }
    }
    
    // Generate tbody for remaining selected labels
    foreach ($selectedLabels as $labelID) {
      $label = $this->labelRepository->find($labelID);
      $labelStubs = $label->getStubsSortedByPosition();
      $newTbody .= $this->generateLabelTbody($label, $labelStubs, $suppress, $currentComments, $notCheckedCheckboxes, $excerpt, $currentLanguage);
    }

    return $newTbody;
  }

  private function generateLabelTbody($label, $labelStubs, $suppress, $currentComments, $notCheckedCheckboxes, $excerpt, $currentLanguage) {
      $newTbody = '';
      $tbodyId = 'oldTbody' . $label->getId();
      $newTbody .= '<tbody id="' . $tbodyId . '" class="sortable">';      


      $trClass = 'table-light hideLabels filtered';
      if ($suppress) {
          $trClass .= ' d-none';
      }
      $newTbody .= '<tr class="' . $trClass . '" id="rowLabel|' . $label->getId() . '">' .
          '<th colspan="5">' . $label->getReportName() . '</th>' .
          '</tr>';
      
      // Sorting buttons
      $newTbody .= '<tr class="sorting-buttons-row filtered">' .
          '<td colspan="5">' . // Extend colspan to cover all columns
          '<div class="d-flex justify-content-start">' . // Flex container for left alignment
          '<button class="btn btn-sm btn-secondary sortAZ me-2" data-tbody="' . $tbodyId . '"><i class="fas fa-sort-alpha-down"></i> A-Z</button>' .
          '<button class="btn btn-sm btn-secondary sortZA" data-tbody="' . $tbodyId . '"><i class="fas fa-sort-alpha-up"></i> Z-A</button>' .
          '</div>' .
          '</td>' .
          '</tr>';

      if (count($labelStubs) > 0) {
        foreach ($labelStubs as $stub) {
          // * Start Text Area
          $textAreaKey = 'labelID=' . $label->getId() . '|stubID=' . $stub->getId();
          $textAreaValue = '';
          foreach ($currentComments as $comment) {
            if ($comment['key'] == $textAreaKey) {
              $textAreaValue = $comment['comment'];
            }
          }
          $textArea = '<textarea name="' . $textAreaKey . '" class="form-control">' . $textAreaValue . '</textarea>';
          // * End Text Area

          // * Start Checkbox
          $inputKey = 'targets|labelID=' . $label->getId() . '|stubID=' . $stub->getId();

          $checked = 'checked';
          foreach ($notCheckedCheckboxes as $checkboxKey) {
            if ($checkboxKey == $inputKey) {
              $checked = '';
            }
          }
          $checkboxInput = '<input type="checkbox" name="' . $inputKey . '" class="form-check-input" ' . $checked . ' />';
          // * End Checkbox

          $descriptionItemClass = 'col-3 descriptionItem';
          $excerptItemClass = 'col-3 excerptItem';
          if ($excerpt) {
            $descriptionItemClass .= ' d-none';
          } else {
            $excerptItemClass .= ' d-none';
          }
          $newTbody .= '<tr id="rowLabel|' . $label->getId() . '|stub|' . $stub->getId() . '">' .
            '<td class="text-end col-2">' .
            $checkboxInput .
            '</td>' .
            '<td class="col-2">' .
            $stub->getName() .
            '</td>' .
            '<td class="' . $descriptionItemClass . '">' .
            substr($stub->getDescription(), 0, 40) .
            '</td>' .
            '<td class="' . $excerptItemClass . '">' .
            substr($stub->getExcerpt(), 0, 40) .
            '</td>' .
            '<td class="col-3">' .
            substr($stub->getBackground(), 0, 40) .
            '</td>' .
            '<td class="border-start">' .
            $textArea .
            '</td>' .
            '</tr>';
        }
      } else {
        $translatedNoSavedTemplates = $this->translator->trans('app-therapy-report-no-saved-templates', [], 'messages', $currentLanguage);
        $newTbody .= '<tr id="rowLabel|' . $label->getId() . '|stub|0">' .
          '<th style="">' .
          '<span class="d-flex justify-content-center fs-4 text-secondary">' .
          $translatedNoSavedTemplates .
          '</span>' .
          '</th>' .
          '</tr>';
      }

      $newTbody .= '</tbody>'; // Close the tbody for each label
      return $newTbody;
  }
  public function generatePDFReport(
    $selectedLabels,
    $suppress,
    $currentComments,
    $notCheckedCheckboxes,
    $stubsOrder,
    $excerpt,
  ): string {
  
    $newTbody = '';
    foreach ($stubsOrder as $order) {
      $labelID = $order[0][0];
      $stubIDs = $order[0][1];
  
      if (in_array($labelID, $selectedLabels)) {
          $index = array_search($labelID, $selectedLabels);
          unset($selectedLabels[$index]);
  
          $label = $this->labelRepository->find($labelID);
          $labelStubs = [];
  
          foreach ($stubIDs as $stubID) {
              $stub = $this->stubRepository->find($stubID);
              if ($stub) {
                  $labelStubs[] = $stub;
              }
          }
  
          $newTbody .= $this->generateLabelTbodyPDF($label, $labelStubs, $suppress, $currentComments, $notCheckedCheckboxes, $excerpt);
      }
    }
  
    // Generate tbody for remaining selected labels
    foreach ($selectedLabels as $labelID) {
      $label = $this->labelRepository->find($labelID);
      $labelStubs = $label->getStubsSortedByPosition();
      $newTbody .= $this->generateLabelTbodyPDF($label, $labelStubs, $suppress, $currentComments, $notCheckedCheckboxes, $excerpt);
    }
  
    return $newTbody;
  
  }
  private function generateLabelTbodyPDF($label, $labelStubs, $suppress, $currentComments, $notCheckedCheckboxes, $excerpt) {
  
    $newTbody = '';
    $trLabel = '';
    if (!$suppress) {
      $trLabel .= '<tr class="table-light" id="rowLabel|' . $label->getId() . '">' .
        '<th colspan="5">' . $label->getReportName() . '</th>' .
        '</tr>';
    }
    $newTbody .= $trLabel;
  
    foreach ($labelStubs as $stub) {
  
      // todo: dont show the line, if checkbox is not checked and show comments in a new row
      // // * Start Checkbox
      $inputKey = 'targets|labelID=' . $label->getId() . '|stubID=' . $stub->getId();
      // check $inputKey if exist in $notCheckedCheckboxes
      $exists = false;
      foreach ($notCheckedCheckboxes as $checkboxKey) {
        if ($checkboxKey == $inputKey) {
          $exists = true;
        }
      }
  
      if ($exists) {
        continue;
      }
  
      $descriptionExcerptItem = '';
      if ($excerpt) {
        $descriptionExcerptItem = '<td ></td><td>' .
          $stub->getExcerpt() .
          '</td>';
      } else {
        // 100px;
        $descriptionExcerptItem = '<td ></td><td>' .
          $stub->getDescription() .
          '</td>';
      }
   
      $newTbody .= '<tr id="rowLabel|' . $label->getId() . '|stub|' . $stub->getId() . '">' .
        '<td class="col-2">' .
        $stub->getName() .
        '</td>' .
        $descriptionExcerptItem .
        '</tr>';
  
      // Comment Section
      $commentKey = 'labelID=' . $label->getId() . '|stubID=' . $stub->getId();
  
      $comment = '';
      foreach ($currentComments as $currentComment) {
        if ($currentComment['key'] == $commentKey) {
          $comment = $currentComment['comment'];
        }
      }
      if ($comment != '') {
        $newTbody .= '<tr><td colspan="3">' . $comment . '</td></tr>';
      }
    }

    return $newTbody;
  }
  
}
