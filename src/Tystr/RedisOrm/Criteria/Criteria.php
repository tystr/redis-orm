<?php

namespace Tystr\RedisOrm\Criteria;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class Criteria implements CriteriaInterface
{
    /**
     * @var Collection
     */
    protected $restrictions;

    /**
     * @param Collection $restrictions
     */
    public function __construct(Collection $restrictions = null)
    {
        if (null === $restrictions) {
            $restrictions = new ArrayCollection();
        }
        $this->restrictions = $restrictions;
    }

    /**
     * @return Collection
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * @param Collection $restrictions
     */
    public function setRestrictions(Collection $restrictions)
    {
        $this->restrictions = $restrictions;
    }

    /**
     * @param Restriction $restriction
     */
    public function addRestriction(RestrictionInterface $restriction)
    {
        $this->restrictions->add($restriction);
    }

    /**
     * @param Restriction $restriction
     */
    public function removeRestriction(RestrictionInterface $restriction)
    {
        $this->restrictions->removeElement($restriction);
    }

    /**
     * @param Restriction $expectedRestriction
     *
     * @return bool
     */
    public function hasRestriction(RestrictionInterface $expectedRestriction)
    {
        foreach ($this->restrictions as $restriction) {
            if ($restriction->equals($expectedRestriction)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $keyGenerator = new RestrictionsKeyGenerator();
        $keyGenerator->getKeyName($this->getRestrictions());
    }
}
