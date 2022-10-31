<?php

namespace App\Twig\Components;

use App\Entity\Therapy\Scheme;
use App\Repository\Therapy\LabelRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;


#[AsLiveComponent('therapy-scheme')]
final class TherapySchemeComponent
{
    use DefaultActionTrait;

    private LabelRepository $labelRepository;

    #[LiveProp(writable: true)]
    public ?string $labelsRequest = null;
    #[LiveProp(writable: true)]
    public ?bool $suppressLabels = null;
    #[LiveProp(writable: true)]
    public ?bool $useExcerpt = null;
    #[LiveProp(writable: true)]
    public bool $isSaved = false;
    #[LiveProp(writable: true)]
    public ?Scheme $template = null;

    public function __construct(LabelRepository $labelRepository)
    {
        $this->labelRepository = $labelRepository;
    }

    public function getLabels(): array
    {
        if ($this->labelsRequest === null and $this->isSaved === false) {
            return [];
        } elseif ($this->labelsRequest === null and $this->isSaved === true) {
            return $this->labelRepository->loadSavedTemplate($this->template);
        } else {
            if ($this->isSaved === false) {
                return $this->labelRepository->findLabelsByRequest($this->labelsRequest);
            } else {
                return $this->labelRepository->loadSavedTemplate($this->template);
            }
        }
    }

    public function getSuppressLabels(): bool
    {
        return $this->suppressLabels ? true : false;
    }

    public function getUseExcerpt(): bool
    {
        return $this->useExcerpt ? true : false;
    }

    public function getLabelsRequest(): string
    {
        return $this->labelsRequest ?? '';
    }

    public function getIsSaved(): bool
    {
        return $this->isSaved;
    }

    public function getTemplateId(): ?int
    {
        return $this->templateId;
    }
}
