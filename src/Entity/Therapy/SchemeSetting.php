<?php

namespace App\Entity\Therapy;

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use App\Repository\Therapy\SchemeSettingRepository;

#[ORM\Entity(repositoryClass: SchemeSettingRepository::class)]
class SchemeSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title = null;
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $objective = null;
    
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $place = null;
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $salutation = null;
    
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $footer = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $textFontStyle = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $titleFontStyle = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $headingFontStyle = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $textFontSize = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $titleFontSize = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $headingFontSize = null;

    #[ORM\Column(type: 'blob', nullable: true)]
    private $logo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;
        return $this;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function setFooter(?string $footer): self
    {
        $this->footer = $footer;
        return $this;
    }

    public function getObjective(): ?string
    {
        return $this->objective;
    }

    public function setObjective(?string $objective): self
    {
        $this->objective = $objective;
        return $this;
    }

    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    public function setSalutation(?string $salutation): self
    {
        $this->salutation = $salutation;
        return $this;
    }

    public function getTextFontStyle(): ?string
    {
        return $this->textFontStyle;
    }

    public function setTextFontStyle(?string $textFontStyle): self
    {
        $this->textFontStyle = $textFontStyle;
        return $this;
    }

    public function getTitleFontStyle(): ?string
    {
        return $this->titleFontStyle;
    }

    public function setTitleFontStyle(?string $titleFontStyle): self
    {
        $this->titleFontStyle = $titleFontStyle;
        return $this;
    }

    public function getHeadingFontStyle(): ?string
    {
        return $this->headingFontStyle;
    }

    public function setHeadingFontStyle(?string $headingFontStyle): self
    {
        $this->headingFontStyle = $headingFontStyle;
        return $this;
    }

    public function getTextFontSize(): ?int
    {
        return $this->textFontSize;
    }

    public function setTextFontSize(?int $textFontSize): self
    {
        $this->textFontSize = $textFontSize;
        return $this;
    }

    public function getTitleFontSize(): ?int
    {
        return $this->titleFontSize;
    }

    public function setTitleFontSize(?int $titleFontSize): self
    {
        $this->titleFontSize = $titleFontSize;
        return $this;
    }

    public function getHeadingFontSize(): ?int
    {
        return $this->headingFontSize;
    }

    public function setHeadingFontSize(?int $headingFontSize): self
    {
        $this->headingFontSize = $headingFontSize;
        return $this;
    }

    public function getLogo()
    {
        if($this->logo) {
            return stream_get_contents($this->logo);
        } else {
            return '';
        }
    }

    public function setLogo($logo): self
    {
        $this->logo = $logo;

        return $this;
    }
}
