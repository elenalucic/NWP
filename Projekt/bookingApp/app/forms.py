from django.contrib.auth.models import User
from django import forms
from django.forms import ModelForm
from .models import Account,Apartman,Booking

class UserForm(ModelForm):
    class Meta:
        model = User
        fields = ['username', 'email']
    password1 = forms.CharField(label='Password', widget=forms.PasswordInput)
    password2 = forms.CharField(label='Password confirmation', widget=forms.PasswordInput)
    def clean_password2(self):
        password1 = self.cleaned_data.get("password1")
        password2 = self.cleaned_data.get("password2")
        if password1 and password2 and password1 != password2:
            raise forms.ValidationError("Passwords don't match")
        return password2


class AccountForm(ModelForm):
    class Meta: 
        model = Account
        fields = ['name']


class ApartmanForm(ModelForm):
    class Meta:
        model = Apartman
        fields= ['title','description', 'location','price','contact_number','apartman_image']



"""
class BookingForm(forms.ModelForm):
    class Meta:
        model = Booking
        fields = ['check_i', 'check_out']
        widgets = {
            'check_in': forms.DateInput(attrs={'type': 'date'}),
            'check_out': forms.DateInput(attrs={'type': 'date'}),
        }
"""

class BookingForm(forms.ModelForm):
   class Meta:
        model = Booking
        fields = ['apartman', 'start_date', 'end_date']
        widgets = {
            'start_date': forms.DateInput(attrs={'type': 'date'}),
            'end_date': forms.DateInput(attrs={'type': 'date'}),
            'apartman': forms.HiddenInput()
        }