from django.db import models
from django.contrib.auth.models import User


class Account(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE)
    name = models.CharField(max_length=100)
   
    def __str__(self):
        return f"{self.id} - {self.name}"

class Apartman(models.Model):
    title = models.CharField(max_length=200)
    description = models.TextField()
    location=models.TextField(default="no location")
    price = models.CharField(max_length=15, null=True, blank=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    contact_number = models.CharField(max_length=15, null=True, blank=True)
    apartman_image = models.ImageField(upload_to='apartman_img/', null=True, blank=True)


"""class Booking(models.Model):
    apartman = models.ForeignKey(Apartman, on_delete=models.CASCADE, related_name='bookings')
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    check_in = models.DateField()
    check_out = models.DateField()

    def __str__(self):
        return f'{self.apartman.title} - {self.check_in} to {self.check_out}'
"""

class Booking(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    apartman = models.ForeignKey(Apartman, on_delete=models.CASCADE)
    start_date = models.DateField()
    end_date = models.DateField()

    def __str__(self):
        return f"{self.user.username} - {self.apartman.title} from {self.start_date} to {self.end_date}"