<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use App\Model\FtpOverview;

/**
 * Presenter
 * @package App\Presenters
 */
final class FtpOverviewPresenter extends Presenter {
    public $ftpOverview;

    /**
     * Constructor with injected model
     * @param FtpOverview $ftpOverview injected model class
     */
    public function __construct(FtpOverview $ftpOverview){
        parent::__construct();
        $this->ftpOverview = $ftpOverview;
    }

    /** view render */
    public function renderDefault($directory = NULL){
		if (!isset($this->template->files)) {
			$this->template->files = $this->ftpOverview->getDirFiles($directory);
			$this->template->prev_directory = $directory;
		}
	}

	/** opens folder and renders view */
	public function handleOpenFolder($directory){
		$file_list = $this->ftpOverview->getDirFiles($directory);

		$this->template->files = $file_list;
		$this->template->prev_directory = $directory;
		$this->redrawControl('filesContainer');
	}

	/** gets file with given name from FTP server and saves it
	 * * @param string    $file_name name of file to download from FTP
	 */
    public function handleDownloadFile(string $file_name, $prev_directory = NULL){
		$this->ftpOverview->getFile($file_name, $prev_directory);
		$this->redirect("FtpOverview:default", $prev_directory);
	}
}