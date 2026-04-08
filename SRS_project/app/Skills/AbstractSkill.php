<?php

namespace App\Skills;

abstract class AbstractSkill implements SkillInterface
{
    /**
     * @var string
     */
    protected string $name = 'Generic Skill';

    /**
     * @var string
     */
    protected string $description = 'A reusable business logic skill.';

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
