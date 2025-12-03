<?php

namespace App\Services;

use App\Models\Leave;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class LeavePdfService
{
    protected $templatePath;

    public function __construct()
    {
        $this->templatePath = storage_path('app/templates/cs-form-no6-template.pdf');
    }

    /**
     * Generate CS Form No. 6 with actual PDF template
     */
    public function generateCsFormNo6(Leave $leave): string
    {
        // Initialize FPDI
        $pdf = new Fpdi();
        
        // Import the template (page 1)
        $pageCount = $pdf->setSourceFile($this->templatePath);
        $templateId = $pdf->importPage(1);
        
        // Add first page
        $pdf->AddPage();
        $pdf->useTemplate($templateId);
        
        // Set font for form fields
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        
        // Fill form fields
        $this->fillEmployeeDetails($pdf, $leave);
        $this->fillLeaveDetails($pdf, $leave);
        $this->fillDatesAndDuration($pdf, $leave);
        
        // Add second page if needed (for continuation)
        if ($pageCount > 1) {
            $pdf->AddPage();
            $templateId2 = $pdf->importPage(2);
            $pdf->useTemplate($templateId2);
            
            $this->fillApprovalDetails($pdf, $leave);
        }
        
        // Add signature if exists
        $this->addSignature($pdf, $leave);
        
        // Save the PDF
        $filename = 'leaves/cs-form-6-leave-' . $leave->id . '-' . time() . '.pdf';
        $outputPath = Storage::path($filename);
        
        $pdf->Output($outputPath, 'F');
        
        return $filename;
    }

    /**
     * Fill employee details section
     */
    protected function fillEmployeeDetails(Fpdi $pdf, Leave $leave): void
    {
        // Office/Department
        $pdf->SetXY(30, 45);
        $pdf->Write(0, $leave->department);
        
        // Name (Last, First, Middle)
        $pdf->SetXY(30, 55);
        $pdf->Write(0, strtoupper($leave->user->last_name));
        $pdf->SetXY(80, 55);
        $pdf->Write(0, strtoupper($leave->user->first_name));
        $pdf->SetXY(130, 55);
        $pdf->Write(0, strtoupper($leave->user->middle_name ?? ''));
        
        // Date of Filing
        $pdf->SetXY(160, 65);
        $pdf->Write(0, $leave->filing_date->format('m/d/Y'));
        
        // Position
        $pdf->SetXY(100, 65);
        $pdf->Write(0, $leave->position);
        
        // Salary
        $pdf->SetXY(160, 75);
        $pdf->Write(0, number_format($leave->salary, 2));
    }

    /**
     * Fill leave details section
     */
    protected function fillLeaveDetails(Fpdi $pdf, Leave $leave): void
    {
        // Leave Type - Check appropriate checkbox
        $this->checkLeaveType($pdf, $leave);
        
        // Leave Details based on type
        $this->fillLeaveTypeDetails($pdf, $leave);
        
        // Number of Working Days
        $pdf->SetXY(160, 145);
        $pdf->Write(0, $leave->days);
        
        // Commutation
        $this->checkCommutation($pdf, $leave);
        
        // Inclusive Dates
        $pdf->SetXY(30, 165);
        $pdf->Write(0, $leave->start_date->format('m/d/Y') . ' - ' . $leave->end_date->format('m/d/Y'));
        
        // Applicant Signature area
        if ($leave->electronic_signature_path) {
            $this->placeSignature($pdf, $leave);
        }
    }

    /**
     * Check the appropriate leave type checkbox
     */
    protected function checkLeaveType(Fpdi $pdf, Leave $leave): void
    {
        $checkboxes = [
            'vacation' => ['x' => 28, 'y' => 95],
            'mandatory' => ['x' => 28, 'y' => 102],
            'sick' => ['x' => 28, 'y' => 109],
            'maternity' => ['x' => 28, 'y' => 116],
            'paternity' => ['x' => 100, 'y' => 95],
            'special_privilege' => ['x' => 100, 'y' => 102],
            'solo_parent' => ['x' => 100, 'y' => 109],
            'study' => ['x' => 100, 'y' => 116],
            'vawc' => ['x' => 28, 'y' => 130],
            'rehabilitation' => ['x' => 100, 'y' => 130],
            'special_women' => ['x' => 28, 'y' => 137],
            'emergency' => ['x' => 100, 'y' => 137],
            'adoption' => ['x' => 28, 'y' => 144],
            'monetization' => ['x' => 100, 'y' => 144],
            'terminal' => ['x' => 28, 'y' => 151],
            'other' => ['x' => 100, 'y' => 151],
        ];

        if (isset($checkboxes[$leave->type])) {
            $coord = $checkboxes[$leave->type];
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetXY($coord['x'], $coord['y']);
            $pdf->Write(0, '4'); // Checkmark in ZapfDingbats
            $pdf->SetFont('Arial', '', 10);
        }
    }

    /**
     * Fill leave type specific details
     */
    protected function fillLeaveTypeDetails(Fpdi $pdf, Leave $leave): void
    {
        switch ($leave->type) {
            case 'vacation':
                if ($leave->leave_location === 'within_philippines') {
                    $pdf->SetXY(28, 175);
                    $pdf->Write(0, '✓');
                } else {
                    $pdf->SetXY(28, 182);
                    $pdf->Write(0, '✓');
                    $pdf->SetXY(45, 182);
                    $pdf->Write(0, $leave->abroad_specify ?? '');
                }
                break;
                
            case 'sick':
                if ($leave->sick_type === 'in_hospital') {
                    $pdf->SetXY(28, 189);
                    $pdf->Write(0, '✓');
                    $pdf->SetXY(45, 189);
                    $pdf->Write(0, $leave->hospital_illness ?? '');
                } else {
                    $pdf->SetXY(28, 196);
                    $pdf->Write(0, '✓');
                    $pdf->SetXY(45, 196);
                    $pdf->Write(0, $leave->outpatient_illness ?? '');
                }
                break;
                
            // Add other leave types as needed
        }
    }

    /**
     * Check commutation option
     */
    protected function checkCommutation(Fpdi $pdf, Leave $leave): void
    {
        if ($leave->commutation === 'requested') {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetXY(148, 145);
            $pdf->Write(0, '4');
            $pdf->SetFont('Arial', '', 10);
        } else {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetXY(165, 145);
            $pdf->Write(0, '4');
            $pdf->SetFont('Arial', '', 10);
        }
    }

    /**
     * Fill dates and duration
     */
    protected function fillDatesAndDuration(Fpdi $pdf, Leave $leave): void
    {
        // This would fill any additional date fields
    }

    /**
     * Fill approval details (page 2)
     */
    protected function fillApprovalDetails(Fpdi $pdf, Leave $leave): void
    {
        // Leave Credits Certification
        if ($leave->credit_as_of_date) {
            $pdf->SetXY(40, 45);
            $pdf->Write(0, $leave->credit_as_of_date->format('m/d/Y'));
            
            $pdf->SetXY(140, 55); // Vacation Total Earned
            $pdf->Write(0, $leave->vacation_earned ?? '0');
            
            $pdf->SetXY(140, 65); // Vacation Less
            $pdf->Write(0, $leave->vacation_less ?? '0');
            
            $pdf->SetXY(140, 75); // Vacation Balance
            $pdf->Write(0, $leave->vacation_balance ?? '0');
            
            $pdf->SetXY(140, 85); // Sick Total Earned
            $pdf->Write(0, $leave->sick_earned ?? '0');
            
            $pdf->SetXY(140, 95); // Sick Less
            $pdf->Write(0, $leave->sick_less ?? '0');
            
            $pdf->SetXY(140, 105); // Sick Balance
            $pdf->Write(0, $leave->sick_balance ?? '0');
        }
        
        // Recommendation
        if ($leave->recommendation === 'approve') {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetXY(28, 125);
            $pdf->Write(0, '4');
            $pdf->SetFont('Arial', '', 10);
        } else {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetXY(28, 132);
            $pdf->Write(0, '4');
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(45, 132);
            $pdf->Write(0, $leave->disapproved_reason ?? '');
        }
        
        // Approval Details
        if ($leave->approved_for) {
            switch ($leave->approved_for) {
                case 'with_pay':
                    $pdf->SetFont('ZapfDingbats', '', 12);
                    $pdf->SetXY(28, 155);
                    $pdf->Write(0, '4');
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetXY(45, 155);
                    $pdf->Write(0, $leave->with_pay_days ?? $leave->days);
                    break;
                    
                case 'without_pay':
                    $pdf->SetFont('ZapfDingbats', '', 12);
                    $pdf->SetXY(28, 162);
                    $pdf->Write(0, '4');
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetXY(45, 162);
                    $pdf->Write(0, $leave->without_pay_days ?? $leave->days);
                    break;
                    
                case 'others':
                    $pdf->SetFont('ZapfDingbats', '', 12);
                    $pdf->SetXY(28, 169);
                    $pdf->Write(0, '4');
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetXY(45, 169);
                    $pdf->Write(0, $leave->others_specify ?? '');
                    break;
            }
        }
        
        // Disapproval reason if applicable
        if ($leave->status === 'rejected' && $leave->disapproved_reason) {
            $pdf->SetXY(28, 175);
            $pdf->Write(0, $leave->disapproved_reason);
        }
    }

    /**
     * Place electronic signature on the form
     */
    protected function placeSignature(Fpdi $pdf, Leave $leave): void
    {
        try {
            $signaturePath = Storage::path($leave->electronic_signature_path);
            
            if (file_exists($signaturePath)) {
                // Get image dimensions
                list($width, $height) = getimagesize($signaturePath);
                
                // Calculate position (adjust coordinates as needed)
                $x = 30;
                $y = 175;
                $maxWidth = 60;
                $maxHeight = 20;
                
                // Calculate scaling to fit within max dimensions
                $scale = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = $width * $scale;
                $newHeight = $height * $scale;
                
                // Add the signature image
                $pdf->Image($signaturePath, $x, $y, $newWidth, $newHeight);
            }
        } catch (\Exception $e) {
            // Fallback: just write the name if signature can't be placed
            $pdf->SetXY(30, 175);
            $pdf->Write(0, 'Electronically signed by: ' . $leave->user->first_name . ' ' . $leave->user->last_name);
        }
    }

    /**
     * Add signature to PDF
     */
    protected function addSignature(Fpdi $pdf, Leave $leave): void
    {
        $this->placeSignature($pdf, $leave);
    }

    /**
     * Download the generated PDF
     */
    public function downloadCsFormNo6(Leave $leave)
    {
        $filePath = $leave->pdf_path;
        
        if (!$filePath || !Storage::exists($filePath)) {
            $filePath = $this->generateCsFormNo6($leave);
            $leave->update(['pdf_path' => $filePath]);
        }
        
        $fileName = "CS-Form-6-Leave-Application-{$leave->id}.pdf";
        
        return Storage::download($filePath, $fileName);
    }
}