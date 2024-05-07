<?php

namespace App\Service;

use App\Repository\Therapy\LabelRepository;
use App\Repository\Therapy\StubCategoryRepository;
use App\Repository\Therapy\StubRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class SchemeService
{

  private $labelRepository;
  private $stubRepository;
  private $stubCategoryRepository;
  private $translator;

  public function __construct(LabelRepository $labelRepository, StubRepository $stubRepository, StubCategoryRepository $stubCategoryRepository, TranslatorInterface $translator)
  {
    $this->labelRepository = $labelRepository;
    $this->stubRepository = $stubRepository;
    $this->stubCategoryRepository = $stubCategoryRepository;
    $this->translator = $translator;
  }

  public function generateTbody(
    $selectedLabels,
    $suppress,
    $currentComments,
    $checkedCheckboxes,
    $stubsOrder,
    $excerpt,
    $currentLanguage
  ): string {
    $newTbody = '';
    $stubsOrder = $stubsOrder ?? [];
    $categories = $this->stubCategoryRepository
        ->createQueryBuilder('stub_category')
        ->orderBy('stub_category.categoryOrder', 'ASC')
        ->getQuery()
        ->getResult();

    // Initialize an array to store categories with their associated stubs
    $categoriesWithStubs = [];
    foreach ($categories as $category) {
        $categoriesWithStubs[$category->getId()] = [
            'stubs' => [],
        ];
    }
    foreach ($stubsOrder as $order) {

        $categoryID = $order[0][0];
        $stubIDs = $order[0][1];
        foreach ($stubIDs as $stubID) {
          $stub = $this->stubRepository->find($stubID);
          if(!in_array($stub, $categoriesWithStubs[$stub->getCategory()->getId()]['stubs'])) {
            $categoriesWithStubs[$stub->getCategory()->getId()]['stubs'][] = $stub;
          }
        }

    }

 
    // Generate tbody for remaining selected labels
    foreach ($selectedLabels as $labelID) {
      $label = $this->labelRepository->find($labelID);
      if(is_null($label)) {
        continue;
      }
      $labelStubs = $label->getStubsSortedByPosition();

      foreach( $labelStubs as $stub) {
        if(!in_array($stub, $categoriesWithStubs[$stub->getCategory()->getId()]['stubs'])) {
          $categoriesWithStubs[$stub->getCategory()->getId()]['stubs'][] = $stub;
        } 
      }
      
    }

    foreach ($categoriesWithStubs as $categoryId => $categoryWithStubs) {
      $stubs = $categoryWithStubs['stubs'];
      if (count($stubs) > 0) {

          $category = $this->stubCategoryRepository->find($categoryId);
          // Generate the tbody for the category
          if($category){
            $newTbody .= $this->generateLabelTbody($category, $stubs, $selectedLabels, $suppress, $currentComments, $checkedCheckboxes, $excerpt, $currentLanguage);
          }
      }
    }

    return $newTbody;

  }

  private function generateLabelTbody($label, $labelStubs, $selectedLabels, $suppress, $currentComments, $checkedCheckboxes, $excerpt, $currentLanguage) {
    $newTbody = '';
    $tbodyId = 'oldTbody' . $label->getId();
    $newTbody .= '<tbody id="' . $tbodyId . '" class="sortable">';      
    

    $trClass = 'table-light hideLabels filtered';
    if ($suppress) {
      $trClass .= ' d-none';
    }
    $newTbody .= '<tr class="' . $trClass . '" id="rowLabel|' . $label->getId() . '">' .
    '<th colspan="6">' . $label->getReportName() . '</th>' .
    '</tr>';
    
    // Sorting buttons
    $newTbody .= '<tr class="sorting-buttons-row filtered">' .
    '<td colspan="6">' . // Extend colspan to cover all columns
    '<div class="d-flex justify-content-start">' . // Flex container for left alignment
    '<button class="btn btn-sm btn-secondary sortAZ me-2" data-tbody="' . $tbodyId . '"><i class="fas fa-sort-alpha-down"></i> A-Z</button>' .
    '<button class="btn btn-sm btn-secondary sortZA" data-tbody="' . $tbodyId . '"><i class="fas fa-sort-alpha-up"></i> Z-A</button>' .
    '</div>' .
    '</td>' .
    '</tr>';
    if (count($labelStubs) > 0) {
      foreach ($labelStubs as $stub) {

        $labels = $stub->getLabels();
        $labelNames = '';
        foreach ($labels as $l) {
          if(in_array($l->getId(), $selectedLabels)) {
            $labelNames .= $l->getReportName() . ', '; // Assuming getName() is the method to get the label name
          }
        }
        $labelNames = rtrim($labelNames, ', ');

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

        $checked = '';
        foreach ($checkedCheckboxes as $checkboxKey) {
          // Define a regular expression pattern
          $pattern = '/labelID=(\d+)\|stubID=(\d+)/';

          // Match the pattern against the string
          preg_match($pattern, $checkboxKey, $matches);

          // Extract the labelID and stubID values
          $stubID = $matches[2];
          if ((int)$stubID == $stub->getId() ) {
            $checked = 'checked';
          }
        }
        $checkboxInput = '<input type="checkbox" name="' . $inputKey . '" class="form-check-input" ' . $checked . ' />';
        // * End Checkbox

        if(!$labelNames) {          
          // Trash icon for removing stub
           $trashIcon = '<button class="btn btn-sm btn-danger trash-icon"><i class="fas fa-trash"></i></button>';
        } else {
          $trashIcon = '';
        }

        $descriptionItemClass = 'col-2 descriptionItem';
        $excerptItemClass = 'col-2 excerptItem';
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
          '<td class="col-2">' .
          substr($stub->getBackground(), 0, 300) .
          '</td>' .
          '<td class="border-start col-2">' .
          $textArea .
          '</td>' .
          '<td class="col-1.5">' .
          $labelNames .
          '</td>' .
          '<td>' .
          $trashIcon .
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
    $checkedCheckboxes,
    $stubsOrder,
    $excerpt,
  ): string {
  
    $newTbody = '';
    $stubsOrder = $stubsOrder ?? [];
    $categories = $this->stubCategoryRepository
        ->createQueryBuilder('stub_category')
        ->orderBy('stub_category.categoryOrder', 'ASC')
        ->getQuery()
        ->getResult();

    // Initialize an array to store categories with their associated stubs
    $categoriesWithStubs = [];
    foreach ($categories as $category) {
        $categoriesWithStubs[$category->getId()] = [
            'stubs' => [],
        ];
    }
    foreach ($stubsOrder as $order) {

        $categoryID = $order[0][0];
        $stubIDs = $order[0][1];


        foreach ($stubIDs as $stubID) {
            $stub = $this->stubRepository->find($stubID);
            if ($stub) {
              $categoriesWithStubs[$categoryID]['stubs'][] = $stub;
            }

        }

    }
    
    // Generate tbody for remaining selected labels
    foreach ($selectedLabels as $labelID) {

      $label = $this->labelRepository->find($labelID);
      if(is_null($label)) {
        continue;
      }
      $labelStubs = $label->getStubsSortedByPosition();
      foreach( $labelStubs as $stub) {
        if(!in_array($stub, $categoriesWithStubs[$stub->getCategory()->getId()]['stubs']))
        $categoriesWithStubs[$stub->getCategory()->getId()]['stubs'][] = $stub; 
      }
      
    }

    foreach ($categoriesWithStubs as $categoryId => $categoryWithStubs) {
      $stubs = $categoryWithStubs['stubs'];
      if (count($stubs) > 0) {

          $category = $this->stubCategoryRepository->find($categoryId);

          // Generate the tbody for the category
          if($category){
            $newTbody .= $this->generateLabelTbodyPDF($category, $stubs, $suppress, $currentComments, $checkedCheckboxes, $excerpt);
          }
          
      }
    }

    return $newTbody;
  
  }
  private function generateLabelTbodyPDF($label, $labelStubs, $suppress, $currentComments, $checkedCheckboxes, $excerpt) {

  
    $newTbody = '';
    $trLabel = '';
    if (!$suppress) {
      $trLabel .= '<tr class="table-light" id="rowLabel|' . $label->getId() . '">' .
        '<th colspan="5">' . $label->getReportName() . '</th>' .
        '</tr>';
    }
    $newTbody .= $trLabel;

    $hasStubs = false;
    foreach ($checkedCheckboxes as $checkboxKey) {
      // Define a regular expression pattern
      $pattern = '/labelID=(\d+)\|stubID=(\d+)/';

      // Match the pattern against the string
      preg_match($pattern, $checkboxKey, $matches);

      // Extract the labelID and stubID values
      $categoryID = $matches[1];
      if ((int)$categoryID == $label->getId() ) {
        $hasStubs = true;
      }
    }

    if(!$hasStubs) {
      return '';
    }
  
    foreach ($labelStubs as $stub) {
  
      // todo: dont show the line, if checkbox is not checked and show comments in a new row
      // // * Start Checkbox
      $inputKey = 'targets|labelID=' . $label->getId() . '|stubID=' . $stub->getId();
      // check $inputKey if exist in $checkedCheckboxes
      $exists = true;
      foreach ($checkedCheckboxes as $checkboxKey) {
        if ($checkboxKey == $inputKey) {
          $exists = false;
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
