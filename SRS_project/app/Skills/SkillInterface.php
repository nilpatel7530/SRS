<?php

namespace App\Skills;

/**
 * Base Interface for all Skills in the system.
 */
interface SkillInterface
{
    /**
     * Get the name of the skill.
     */
    public function getName(): string;

    /**
     * Get the description of the skill.
     */
    public function getDescription(): string;
}
