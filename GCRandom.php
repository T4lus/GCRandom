<?php

namespace GCRandom;

define('GC_RAND_MAX',   0x7fffffff);

class GCRandom
{	
	private $seed = time();
	private $gaussContext = array(
		"phase"     => 0,
		"V1"        => 0,
		"V2"        => 0,
		"S"         => 0,
	);


	/**
	 * Set Random Seed
	 *
	 * @param int    $seed
	 * @return int
	 */
	public function setSeed($seed) {
		$this->seed = $seed;
	}


	/**
	 * Get Random Seed
	 *
	 * @return int
	 */
	public function getSeed() {
		return $this->seed;
	}


	/**
	 * Get Maximum Random Number
	 *
	 * @return int
	 */
	public function getRandMax() {
		return GC_RAND_MAX;
	}


	/**
	 * Get Random integer
	 *
	 * @param int    $_min
	 * @param int 	 $_max
	 * @return int
	 */
	public function rand($_min = 0, $_max = GC_RAND_MAX) {
		$this->seed = ($this->seed * 1103515245 + 12345) & GC_RAND_MAX;
		$number = (($this->seed >> 1) & GC_RAND_MAX);
		
		if ($_min > $_max) { $Min = $_max; $Max = $_min; }
		else { $Min = $_min; $Max = $_max; }
		if ($Max - $Min)
			$number = ($number % ($Max + 1 - $Min)) + $Min;

		return $number;
	}


	/**
	 * Get Random float
	 *
	 * @param float    $_min
	 * @param float	   $_max
	 * @param int	   $round
	 * @return int
	 */
	public function randFloat($_min = 0, $_max = 1, $round = -1) {
		$randomfloat = $this->rand();
		$Min = 0;
		$Max = 1;

		if ($_min > $_max) { $Min = $_max; $Max = $_min; }
		else { $Min = $_min; $Max = $_max; }
		if ($Max - $Min)
			$randomfloat = (float)($randomfloat % ($Max + 1 - $Min)) + $Min + ($randomfloat / GC_RAND_MAX); //$randomfloat = (float)($Min * $randomfloat / GC_RAND_MAX) + (($Max + 1 - $Min) / 2); //

		if($round >= 0)
			$randomfloat = round($randomfloat, $round);

		return $randomfloat;
	}


	/**
	 * Get Gaussian Random Float
	 *
	 * @param float    $dev
	 * @param int      $round
	 * @return float
	 */
	public function randGauss($dev = 1.0, $round = -1) {
		$X = 0;
	   
		if ($this->gaussContext["phase"] === 0) {
			do {
				$this->gaussContext["V1"] = $this->randFloat(-1, 1, 2);
				$this->gaussContext["V2"] = $this->randFloat(-1, 1, 2);
				$this->gaussContext["S"] = $this->gaussContext["V1"] * $this->gaussContext["V1"] + $this->gaussContext["V2"] * $this->gaussContext["V2"];
			} while ($this->gaussContext["S"] >= 1 || $this->gaussContext["S"] == 0);

			$X = $this->gaussContext["V1"] * sqrt(-2 * log( $this->gaussContext["S"]) /  $this->gaussContext["S"]);
		} 
		else {
			$X = $this->gaussContext["V2"] * sqrt(-2 * log( $this->gaussContext["S"]) /  $this->gaussContext["S"]);
		}
		$this->gaussContext["phase"] = 1 - $this->gaussContext["phase"];

		$number = $X * $dev;

		if($round >= 0)
			$number = round($number, $round);

		return $number;
	}


	/**
	 * Get User Profile Info
	 *
	 * @param int    $id
	 * @param string $fields
	 * @return UserProfile
	 */
	public function randWeight($Ary) {
		$totalWeight = 0;
		foreach ($Ary as $key => $value) {
			$totalWeight += $value['weight'];
		}

		$randType = $this->floatRand(0, $totalWeight-1, 2);
		foreach ($Ary as $key => $value) {
			if ($randType < $value['weight'])
				return $key;
			$randType -= $value['weight'];
		}
	}


	/**
	 * Get Random key from array
	 *
	 * @param array    $Ary
	 * @return int
	 */
	public function randArray($Ary) {
		if (count($Ary) > 1)
			$key = $this->rand(0, count($Ary) - 1);
		else
			$key = 0;
		return $key;
	}


	/**
	 * Shuffle array item
	 *
	 * @param array    $Ary
	 * @return array
	 */
	public function shuffleArray($Ary = array()) {
		$copy = array();

		while (count($Ary)) {
			$key = $this->randArray($Ary);
			$copy[] = $Ary[$key];
			unset($Ary[$key]);
			$Ary = array_values($Ary);
		}
		return $copy;
	}

}