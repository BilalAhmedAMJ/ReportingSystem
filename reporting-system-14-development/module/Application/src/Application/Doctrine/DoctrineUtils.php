<?

namespace Application\Util;

use Doctrine\Common\Collections\ArrayCollection;

class DoctrineUtils {
	
	/**
	 * @param array... $arrayCollections
	 * @return ArrayCollection
	 */
	public function merge(...$arrayCollections)
	{
	    $returnCollection = new ArrayCollection();
	
	    /**
	     * @var ArrayCollection $arrayCollection
	     */
	    foreach ($arrayCollections as $arrayCollection) {
	        if ($returnCollection->count() === 0) {
	            $returnCollection = $arrayCollection;
	        } else {
	            $arrayCollection->map(function ($element) use (&$returnCollection) {
	                if (!$returnCollection->contains($element)) {
	                    $returnCollection->add($element);
	                }
	            });
	        }
	    }
	
	    return $returnCollection;
	}	
}
