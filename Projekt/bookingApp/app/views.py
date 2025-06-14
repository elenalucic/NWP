from django.shortcuts import render, get_object_or_404, redirect
from django.http import JsonResponse, HttpResponseRedirect
from django.urls import reverse
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from .models import Apartman, Booking
from .forms import ApartmanForm, BookingForm

def home(request):
    apartmans = Apartman.objects.all()
    form = ApartmanForm()

    if request.method == 'POST' and request.user.is_superuser:
        form = ApartmanForm(request.POST, request.FILES)
        if form.is_valid():
            apartman = form.save(commit=False)
            apartman.user = request.user
            apartman.save()
            if request.is_ajax():
                return JsonResponse({'success': True})
            return HttpResponseRedirect(reverse('app:home'))
        else:
            if request.is_ajax():
                return JsonResponse({'success': False, 'errors': form.errors})
    
    context = {
        'apartmans': apartmans,
        'form': form
    }
    return render(request, 'app/home.html', context)


def apartman_detail(request, apartman_id):
    apartman = get_object_or_404(Apartman, id=apartman_id)
    bookings = Booking.objects.filter(apartman=apartman)
    return render(request, 'app/apartman_detail.html', {'apartman': apartman, 'bookings': bookings, 'form': BookingForm(initial={'apartman': apartman})})

@login_required
def add_booking(request):
    if request.method == 'POST':

        form = BookingForm(request.POST)
        if form.is_valid():
            new_booking = form.save(commit=False)
            apartman = new_booking.apartman
            start_date = new_booking.start_date
            end_date = new_booking.end_date
            
            overlapping_bookings = Booking.objects.filter(
                apartman=apartman,
                start_date__lte=end_date,
                end_date__gte=start_date
            )
            
            if overlapping_bookings.exists():
                return JsonResponse({'success': False, 'message': 'This apartment is already booked for the selected dates.'}, status=400)
            
            new_booking.user = request.user
            new_booking.save()
            return JsonResponse({'success': True})
        else:
            return JsonResponse({'success': False, 'errors': form.errors}, status=400)
    return JsonResponse({'success': False, 'message': 'Invalid request method.'}, status=400)


def fetch_bookings(request, apartman_id):
    bookings = Booking.objects.filter(apartman_id=apartman_id)
    booking_list = [{
        'title': 'Booked',
        'start': booking.start_date,
        'end': booking.end_date,
        'color': 'red'
    } for booking in bookings]
    return JsonResponse(booking_list, safe=False)

@login_required
def delete_apartman(request, apartman_id):
    apartman = get_object_or_404(Apartman, pk=apartman_id)
    if request.method == 'POST':
        apartman.delete()
        return redirect('app:home')
    return redirect('app:home')  


@login_required
def edit_apartman(request, apartman_id):
    apartman = get_object_or_404(Apartman, id=apartman_id)
    if request.method == 'POST':
        form = ApartmanForm(request.POST, request.FILES, instance=apartman)
        if form.is_valid():
            form.save()
            return HttpResponseRedirect(reverse('app:apartman_detail', args=[apartman.id]))
    else:
        form = ApartmanForm(instance=apartman)
    return render(request, 'app/edit_apartman.html', {'form': form, 'apartman': apartman})

@login_required
def add_apartman(request):
    if request.method == 'POST':
        form = ApartmanForm(request.POST, request.FILES)
        if form.is_valid():
            apartman = form.save(commit=False)
            apartman.user = request.user
            apartman.save()
            return HttpResponseRedirect(reverse('app:apartman_detail', args=[apartman.id]))
        else:
            errors = form.errors.as_json()
            return JsonResponse({'success': False, 'errors': errors}, status=400)
    else:
        form = ApartmanForm()
        context = {
            'form': form,
        }
        return render(request, 'app/add_apartman.html', context)
    

