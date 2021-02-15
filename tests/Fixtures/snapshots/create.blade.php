<div>
    <label for="avatar">
        Avatar
    </label>

    <input name="avatar" type="file" />

    @error('avatar')
    <div style="color: red;">
        {{ $message }}
    </div>
    @enderror
</div>

<div>
    <label for="first_name">
        First Name
    </label>

    <input name="first_name" type="text" value="{{ old('first_name') }}" />

    @error('first_name')
    <div style="color: red;">
        {{ $message }}
    </div>
    @enderror
</div>

<div>
    <label for="bio">
        Bio
    </label>

    <textarea name="bio" rows="5">{{ old('bio') }}</textarea>

    @error('bio')
    <div style="color: red;">
        {{ $message }}
    </div>
    @enderror
</div>
