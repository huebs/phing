<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

require_once 'phing/Task.php';

/**
 * MakeDirSafeTask
 *
 *
 *
 * @author      Ben Huebscher <ben@hubtech.tv>
 * @license     MIT
 * @version     $Id$ $Rev $Id$ $Author$
 * @package     phing.tasks.ext
 */
class MakeDirSafeTask extends Task {

    /**
     * String used to set the root directory to be made safe.
	 *
     * @var PhingFile
     */
	private $baseDir;

	/**
	 * Name of the safe file.
	 *
	 * @var string
	 */
	private $filename = "index.html";

    /**
     * Contents of the safe file.
	 *
     * @var string
     */
	private $contents = <<<HTML
<!DOCTYPE html><title></title>

HTML;

    /**
     * Internal Array of Strings used to store the list of directories to be made safe.
	 *
     * @var PhingFile[]
     */
	private $directories = array();

	/**
	 *  main()
	 *
	 *
	 */
	public function main() {
		$this->_validateAttributes();
		$this->_findSubdirectories($this->baseDir);
		$this->_makeSafe();

		$this->log("Protected dir: $this->baseDir");
	}

	/**
	 * This is the base directory. It and all of its child directories will be made safe.
	 *
	 * @param PhingFile $baseDir
	 */
	public function setBaseDir(PhingFile $baseDir)
	{
		$this->baseDir = $baseDir;
	}

	/**
	 *  getBasedir()
	 *
	 * @return string
	 */
	public function getBaseDir() {
		return $this->baseDir;
	}

	/**
	 *  setSafeFileContents()
	 *
	 * @param string $filename
	 */
	public function setFilename($filename)
	{
		$this->filename = (string) $filename;
	}

	/**
	 *  getSafeFileName()
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 *  setSafeFileContents()
	 *
	 * @param string $contents
	 */
	public function setContents($contents) {
		$this->contents = (string) $contents;
	}

	/**
	 *  getSafeFileContents()
	 *
	 *
	 * @return string
	 */
	public function getContents() {
		return $this->contents;
	}

	/**
	 * Validates attributes coming in from XML
	 *
	 * @access  private
	 * @return  void
	 * @throws  BuildException
	 */
	protected function _validateAttributes()
	{
		if (!$this->baseDir || $this->baseDir->isDirectory()) {
			throw new BuildException("Base Directory must be set.");
		}

		if (!$this->filename) {
			throw new BuildException("Name for safe file must be set");
		}

		if (!$this->contents) {
			throw new BuildException("Contents of the safe file must be set");
		}
	}

	/**
	 *  _findSubdirectories()
	 *
	 * @param PhingFile $directory
	 */
	protected function _findSubdirectories(PhingFile $directory) {
		if ($directory->isDirectory()) {
			$this->directories[] = $directory;
			$directoryContents = $directory->listFiles();

			foreach($directoryContents as $item) {
				if ($item instanceof PhingFile && $item->isDirectory()) {
					$this->_findSubdirectories($item);
				}
			}
		}
	}

	/**
	 * _makeDirectoriesSafe()
	 *
	 */
	protected function _makeSafe()
	{
		foreach ($this->directories as $directory) {
			$safeFile = new PhingFile($directory, $this->filename);
			if (!$safeFile->exists()) {
				file_put_contents($safeFile->getAbsolutePath(), $this->contents);
			}
		}
	}
}
