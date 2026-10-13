<div class="form-group full">
    <label for="required_job_position">Required Staff Job Position</label>
    <select id="required_job_position" name="required_job_position">
        <option value="">Not configured</option>
        @foreach(\App\Models\Staff::LAUNDRY_JOB_POSITIONS as $position)
            <option value="{{ $position }}" @selected(old('required_job_position', $service->required_job_position ?? '') === $position)>{{ $position }}</option>
        @endforeach
    </select>
    <small>Unmapped services cannot be assigned until their required job position is configured.</small>
    @error('required_job_position')<div class="error">{{ $message }}</div>@enderror
</div>