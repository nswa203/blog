#!/usr/bin/python
#
# OST50.py v2.5, for Python 2.7
#
# Returns OS Terrain 50 height data ...
#	Either:	Of a single point (x1,y1)
#	Or:	Of an elevation profile between two points (x1,y1) and (x2,y2)
#
# The data returned as JSON(P) is as follows:
#	Either:
#		ht	Elevation (metres referenced to OSGM02 - almost sea-level)
#	Or:
#		dist	Horizontal distance (kilometres)
#		surface	Surface distance over the actual terrain (kilometres)
#		ascent	Surface distance ascending (kilometres)
#		level	Surface distance level (kilometres)
#		descent	Surface distance descending (kilometres)
#		min	Minimum elevation (metres referenced to OSGM02)
#		max	Maximum elevation (ditto)
#		average	Average elevation (ditto)
#		minGr	Minimum gradient (calculated as vertical change in height over horizontal distance)
#		maxGr	Maximum gradient (ditto)
#		aveGr	Average gradient (ditto)
#		prof	An array of profile points (optional)
#		url	Google Static Image Charts URL to obtain a profile diagram (optional)
#
# Optionally for the profile, an array of up to 100 samples can be returned both as JSON and as a Google Static
# Image Charts URL.  If the path covers more than 100 OST50 cells, then, depending on the absence or presence
# of an mx=true parameter, each point returned represents either the average (by default) or maximum height
# that the path encounters from those cells in the neighbourhood of that point.
#
# The data columns returned in the profile have the following meaning:
#	Easting (metres referenced to OSGB36)
#	Northing (ditto)
#	Average or maximum height of terrain (metres referenced to OSGM02)
#
# Before submitting the URL to the Google Static Image Charts API, ~Width~ and ~Height~ must be replaced
# by suitable dimensions in pixels, the product (multiplication) of the two being less than Google's limit
# of 300,000  -  see the Google Static Image Charts API documentation for details:
#	https://developers.google.com/chart/image/
# Note: Google Static Image Charts API has been deprecated by Google since 2012, but they have made unofficial
# noises about not turning it off or else replacing it with something similar.
#
# Usage (without the spaces and line breaks that have been inserted here for clarity):
#	http://<host>/cgi-bin/OST50.py ? x1=<x1> & y1=<y1> [ & x2=<x2> & y2=<y2> ]
#					[ & cb=<callback function name> ]
#					[ & pr=True ] [ & mx=True ]
#					[ & db=True ]
#
# Where:
#	x1	Easting of first point (required, metres referenced to OSGB36)
#	y1	Northing of first point (ditto)
#	x2	Easting of second point (required for profile, metres referenced to OSGB36)
#	y2	Northing of second point (ditto)
#	cb	Optionally request the data be returned as a JSONP function call back
#			with the JSON data as the single argument to the call
#			(enables the returned data to be read as valid JavaScript by a <script> tag)
#	pr	Optionally request a full profile array and a Google Static Image Charts URL to draw it
#	mx	Optionally, have each sample point returned be the maximum, rather than the default average,
#			of the OST50 heights in the neighbourhood of the point.
#	db	Optionally request output in the form of HTML for debugging purposes
#	vb	As above, but more verbose
# And:
#	Failure to provide any of the first two or four required parameters returns a help message
########################################################################################################
# NS1 identifies fixes for later versions of Python
# NS2 identifies mod to support command line arguments 
########################################################################################################

import os
import sys

import cgi
import cgitb
cgitb.enable()
# cgitb.enable(display=0, logdir="/logfiles")

# Template location
TempPath = os.path.join( "..", "Resources", "Templates", os.path.splitext( os.path.basename(__file__) )[0] + ".html" )

# Page title - choose your own, for example your website
PageTitle = "&lt;example.com&gt;"

import array
import json
import re
import string
#from string import lower, upper NS1
import zipfile

from datetime import datetime, timedelta

from math import acos
from math import atan
from math import cos
from math import sin
from math import degrees
from math import radians
from math import ceil
from math import floor
from math import log
from math import sqrt

# OST50 constants
# Easting and northing tile coverage limits
# (these cover England, Scotland, Wales)
XMin	= 0
XMax	= 700000
YMin	= 0
YMax	= 1300000
# Subfolder containing OST50 files
OST50Pth = os.path.join( "OST50", "data" )
# Zip file name template
ZipFN	= "_OST50GRID_20130401.zip"
# Tile count
Tiles	= 0

# Conversions & geographical constants
# Miles per kilometre
MPK 	= 0.6213
# Feet per metre
FPM 	= 3.2808
# Geometric mean radius of earth (m)
Re	= float(6371001)

# Chart constants for Google Static Image Charts API
# Maximum reliable sample limit (determined empirically)
stLim	= 100
# Colors
black	= "000000"
white	= "CCCCCC"
terr	= "009900"
earth	= black
sky1	= "336699"
sky2	= "6699CC"
axes	= black
# Font size for axis labels
font	= "10"
# Axis and line thickness
thick	= 6

TRUE	= ("true", "t", "yes", "y", "1")

# print "Content-Type: text/html\n\n"

# Ordnance Survey Terrain 50 (OST50) elevation tile:
#	http://www.ordnancesurvey.co.uk/business-and-government/products/terrain-50.html
# Format: 200 x 200 pixels per 10km2 tile, X east within Y north, starting from north west corner.
class OST50Tile:

	# Tile width (sample points)
	ncols	= 200
	# Tile height (sample points)
	nrows	= 200
	# Lower left cell easting (metres)
	xll	= 0
	# Lower left cell northing (metres)
	yll	= 0
	# Cell size (metres)
	cell	= 50
	# Terrain Height Data
	data	= None
	# Time of last access
	access	= 0

	def __init__( self, cols, rows, x, y, cell, data ):
		if isinstance( cols, int ):
			self.ncols	= cols
			self.nrows	= rows
			self.xll	= x
			self.yll	= y
			self.cellsize	= cell
			self.data	= data
		else:
			start		= GridRefToEN( cols )
			self.xll	= start.x
			self.yll	= start.y
			self.data	= None
		self.access	= datetime.now()

	# Tile width
	def getCols( self ):
		return self.ncols

	# Tile height
	def getRows( self ):
		return self.nrows

	# Lower left cell easting
	def getXLL( self ):
		return self.xll

	# Lower left cell northing
	def getYLL( self ):
		return self.yll

	# Cell size (metres)
	def getCell( self ):
		return self.cell

	# Terrain Height Data
	def getData( self ):
		return self.data

	# Terrain Height Data
	def getAccess( self ):
		return self.access

	# Log access time
	def setAccess( self, sTime ):
		if sTime:
			self.access	= sTime
		else:
			self.access	= datetime.now()

class OST50TileSet:

	# Terrain height file extension
	ext	= ".asc"
	# Minimum height (metres)
	min	= -32767
	# Maximum height (metres)
	max	= 32767
	# Cell size (metres)
	cell	= 50
	# OST50 Tile Set
	tiles	= dict()

	def __init__( self ):
		if self.tiles == None:
			self.tiles	= dict()

	# Get cell size in metres
	def getCell( self ):
		return self.cell

	# Get tile data
	def getData( self, tile ):
		return self.tiles[tile].getData()

	# Calc OST50 filename
	def getOST50Name( self, east, north ):
		return ENToGridRef( east, north )

	# Read OST50 file (degrees in)
	def getOST50Data( self, east, north ):
		global Tiles
		tn = self.getOST50Name( east, north )
		if tn not in self.tiles:
			data = asc = zip = None
			fn = os.path.join( OST50Pth, tn[0:2].lower() )
			try:
				# print "/* Looking for file %s */<br>\n" % os.path.join(fn, tn.upper() + self.ext)
				asc = open( os.path.join(fn, tn.upper() + self.ext), "r" )
			except IOError:
				try:
					# print "/* Looking for file %s */<br>\n" % os.path.join(fn, tn.lower() + ZipFN)
					#print(os.path.join(fn, tn.lower() + ZipFN))
					zip = zipfile.ZipFile( os.path.join(fn, tn.lower() + ZipFN), "r" )
					asc = zip.open( tn.upper() + self.ext )
				except IOError:
					# print "/* Could not open data file: %s */<br>\n" % fn
					pass
			if asc != None:
				# print "/* Opened file %s */<br>\n" % fn
				for y in range( 5 ):
					try:
						li = asc.readline().split()
						if re.compile( "^ncols$", re.IGNORECASE ).match( li[0] ):
							ncols	= int( li[1] )
						elif re.compile( "^nrows$", re.IGNORECASE ).match( li[0] ):
							nrows	= int( li[1] )
						elif re.compile( "^xllcorner$", re.IGNORECASE ).match( li[0] ):
							xll	= int( li[1] )
						elif re.compile( "^yllcorner$", re.IGNORECASE ).match( li[0] ):
							yll	= int( li[1] )
						elif re.compile( "^cellsize$", re.IGNORECASE ).match( li[0] ):
							cell	= int( li[1] )
						# print "/* li[0]: %s, li[1]: %s */<br>/n" % ( li[0], li[1] )
					except Exception:
						# print "/* Could not read or bad data %s in header line %d of file %s */<br>\n" %( li[1], y, fn )
						pass

				data	= [""] * ncols * nrows
				for y in range( nrows ):
					try:
						li	= asc.readline().split()
						for x in range( ncols ):
							try:
								data[(nrows - 1 - y)*ncols + x] = li[x]
							except Exception:
								# print "/* Bad data %s in line %d column %d of file %s */<br>\n" %( li[x], y + 5, x, fn )
								pass
					except Exception:
						# print "/* Could not read line %d of data file: %s */<br>\n" % (y, fn)
						pass
				asc.close()
			if zip != None:
				zip.close()
			if data:
				# print "/* Loaded Tile: %s */<br>\n" % tn
				self.tiles[tn] = OST50Tile( ncols, nrows, xll, yll, cell, data )
				Tiles += 1
			else:
				# print "/* Blank Tile: %s */<br>\n" % tn
				self.tiles[tn] = OST50Tile( tn, None, None, None, None, None )
			while len( self.tiles ) >= 6:
				oldest	= ""
				#keys	= self.tiles.iterkeys() 										# NS1
				keys	= self.tiles.keys()

				for t in keys:
					# print "/* Oldest: %s, This: %s */<br>\n" % ( oldest, t )
					if (oldest == "") or (self.tiles[t].getAccess() < self.tiles[oldest].getAccess()):
						oldest = t
				if oldest != "":
					# print "/* Removing %s */<br>\n" % ( oldest )
					del( self.tiles[oldest] )
			# keys = self.tiles.iterkeys()
			# print "/*\nTile cache contents now (%d tiles):" % len( self.tiles )
			# for t in keys:
			#	print " %s" % ( t )
			# print "*/<br>\n"
		return self.tiles[tn]

	# Look up raw height
	def getCleanHt( self, x, y ):
		tile	= self.getOST50Data( x, y )
		ht	= 0
		if tile:
			tile.setAccess( None )
			if tile.getData():
				x	= int( round((x - tile.getXLL()) / tile.getCell()) )
				y	= int( round((y - tile.getYLL()) / tile.getCell()) )
				of	= y*tile.getCols() + x
				ht	= float( tile.getData()[of] )
				# print "/* x:%u, y:%u, of:%u, ht:%d, len:%u */<br>\n" % ( x, y, of, ht, len(tile.getData()) )
		return ht

	# Look up and interpolate height ( EN duple in)
	def getHeight( self, d ):
		ht = 0
		cx = float( d.x ) / self.cell
		cy = float( d.y ) / self.cell
		x0 = floor( cx )
		y0 = floor( cy )
		x1 = x0 + 1
		y1 = y0 + 1
		dx = cx - x0
		dy = cy - y0

		# Adjust for tile cell coordinates being of SW corner, not the middle
		if dx < 0.5:
			if x0 > 0:	# Doubt if this will ever fail in practice
				x0 -= 1
			x1 -= 1
			dx += 0.5
		else:
			dx -= 0.5
		if dy < 0.5:
			if y0 > 0:	# Doubt if this will ever fail in practice
				y0 -= 1
			y1 -= 1
			dy += 0.5
		else:
			dy -= 0.5

		x0 *= self.cell
		y0 *= self.cell
		x1 *= self.cell
		y1 *= self.cell
		# print "/* x:%.0f, y:%.0f, cx:%.3f, cy:%.3f, x0:%u, y0:%u, x1:%u, y1:%u, dx:%.3f, dy:%.3f */<br>\n" % (d.x,d.y,cx,cy,x0,y0,x1,y1,dx,dy)

		hSW = self.getCleanHt( x0, y0 )
		hSE = self.getCleanHt( x1, y0 )
		hNW = self.getCleanHt( x0, y1 )
		hNE = self.getCleanHt( x1, y1 )
		hS = ( 1 - dx )*hSW + dx*hSE
		hN = ( 1 - dx )*hNW + dx*hNE
		ht = ( 1 - dy )*hS + dy*hN
		# print "/* x:%.0f, y:%.0f, cx:%.3f, cy:%.3f, x0:%u, y0:%u, x1:%u, y1:%u, dx:%.3f, dy:%.3f, hSW:%d, hSE:%d, hNW:%d, hNE:%d, hS:%.3f, hN:%.3f, ht:%.3f  */<br>\n" % (d.x,d.y,cx,cy,x0,y0,x1,y1,dx,dy,hSW,hSE,hNW,hNE,hS,hN,ht)
		return int( ht )

# The current OST50 tiles
OST50 = OST50TileSet()


# Cartographical geometry

# Class defining a 2D coordinate
class Duple:
	def __init__( self, xx, yy ):
		self.x = xx
		self.y = yy

# UK East,North to GridRef
def ENToGridRef( anEast, aNorth ):
	result	= ""
	pow10	= 100000
	anEast 	+= 10*pow10
	aNorth 	+= 5*pow10
	anEastH	= floor( anEast / pow10 )
	aNorthH	= floor( aNorth / pow10 )
	anEastL	= floor( anEast % pow10 )
	aNorthL	= floor( aNorth % pow10 )
	nDig	= 2
	for i in range( nDig ):
		eSq = nSq = ""
		if i == 0:
			eSq	= floor( anEastH / 5 )
			nSq	= floor( aNorthH / 5 )
		else:
			eSq	= anEastH % 5
			nSq	= aNorthH % 5
		square	= int( (5*( 4 - nSq ) + eSq) % 25 )
		if square > 7:
			square += 1
		print('#################################2#',square, chr(65+square))	
		result += chr( 65 + square )
	nDig	= 1
	pow10	= 10**(5 - nDig)
	if nDig > 0:
		nDig = "%d" % nDig
		nDig = "%0" + nDig + "d%0" + nDig + "d"
		result += nDig % ( floor(anEastL/pow10), floor(aNorthL/pow10) )
	print('#################################1#',anEast,aNorth,result)
	return result

# UK GridRef to East,North
def GridRefToEN( aGridRef ):
	result	= None
	nDig	= len(aGridRef)
	if ( nDig <= 12 ) and ( nDig % 2 == 0 ):
		east	= 0
		north	= 0
		nDig	= min( 2, nDig )
		pow10	= 100000
		for i in range( nDig ):
			if ( aGridRef[i] >= "A" ) and ( aGridRef[i] <= "Z" ):
				square	= ord( aGridRef[i] ) - ord( "A" )
				if square > 7:
					square = square - 1
				eSq = floor(square % 5)
				nSq	= 4 - floor(square / 5)
				if i == 0:
					eSq = (eSq - 2)*5
					nSq = (nSq - 1)*5
				east	+= eSq*pow10
				north	+= nSq*pow10
		nDig	= max( 0, (len(aGridRef) - 2)/2 )
		pow10	= 10**( 5 - nDig )
		if nDig > 0:
			nDig=int(nDig) 											# NS01
			pow10=int(pow10) 										# NS01
			east	+= int( aGridRef[2:2+nDig], 10 )*pow10
			north	+= int( aGridRef[2+nDig:2+2*nDig], 10 )*pow10
		result	= Duple( east, north )
	return result

# Routines for creating Google Static Image Charts URL
def axisStep( range, minNum ):
	if range:
		interval = 10**floor( log(range)/log(10) )
		turn	= False
		while range/interval < minNum:
			interval = interval / (2 if turn else 5)
			turn = not turn
	else:
		interval = 0.0005
	return interval

def extEncodeGoogle( aValue, aMin, aMax ):
	result	= None
	if (aMin <= aValue) and (aValue <= aMax):
		value	= (aValue-aMin)*4096/abs(aMax - aMin)
		dig1	= simEncodeGoogle( floor(value/64), 0, 64, True )
		dig2	= simEncodeGoogle( floor(value%64), 0, 64, True )
		result	= dig1 + dig2
	return result

def simEncodeGoogle( aValue, aMin, aMax, extDigit ):
	result	= None
	if (aMin <= aValue) and (aValue <= aMax):
		value	= int(round( (aValue - aMin)*(64 if extDigit else 61 )/abs(aMax - aMin) ))
		if value < 26:
			result = chr( ord("A") + value )
		elif value < 52:
			result = chr( ord("a") + (value - 26) )
		elif value < 62:
			result = chr( ord("0") + (value - 52) )
		elif extDigit and (value == 62):
			result = "-"
		elif extDigit and (value == 63):
			result = "."
	return result


def main( pars ):

		global Tiles

		resp	= ""
		error = x1 = y1 = x2 = y2 = cb = pr = mx = db = vb = par = None

		try:
			#x1	= float( pars.getvalue("x1") ) NS02
			x1 = float(sys.argv[1])			
		except Exception:
			error	= True
			resp	+= "<p>ERROR: required x1 parameter missing!</p>\n"

		try:
			#y1	= float( pars.getvalue("y1") ) NS02
			y1 = float(sys.argv[2])			
		except Exception:
			error	= True
			resp	+= "<p>ERROR: required y1 parameter missing!</p>\n"

		try:
			#x2	= float( pars.getvalue("x2") ) NS02
			x2 = float(sys.argv[3])			
		except Exception:
			# error	= True
			# resp	+= "<p>ERROR: required x2 parameter missing!</p>\n"
			pass

		try:
			#y2	= float( pars.getvalue("y2") ) NS02
			y2 = float(sys.argv[4])
		except Exception:
			# error	= True
			# resp	+= "<p>ERROR: required y2 parameter missing!</p>\n"
			pass

		try:
			#par	= pars.getvalue("cb") NS02
			par = sys.argv[5]
			if par:
				cb = par
		except Exception:
			pass

		try:
			#par	= pars.getvalue("pr") NS02
			par = sys.argv[6]
			if par.lower() in TRUE:
				pr = True
		except Exception:
			pass

		try:
			par	= pars.getvalue("mx")
			if par.lower() in TRUE:
				mx = True
		except Exception:
			pass

		try:
			par	= pars.getvalue("db")
			if par.lower() in TRUE:
				db = True
		except Exception:
			pass

		try:
			par	= pars.getvalue("vb")
			if par.lower() in TRUE:
				vb = db = True
		except Exception:
			pass

		if (x1 != None) and (x1 >= XMin) and (x1 < XMax) and (y1 != None) and (y1 >= YMin) and (y1 < YMax) and (x2 != None) and (x2 >= XMin) and (x2 < XMax) and (y2 != None) and (y2 >= YMin) and (y2 < YMax):

			if db:
				t0	= datetime.now()

			Tiles	= 0

			d	= sqrt( (x2-x1)**2 + (y2-y1)**2 )
			nS	= max( int(ceil(d/OST50.cell)), 1 )
			dS	= d / nS

			# Set up profile calculations
			if pr:
				nP	= min( nS, stLim - 1 )
				stP	= d / nP
				points	= []
				if db:
					prof	= "\"prof\":[<br>\n"
				thisP	= 0
				nextP	= 1
				lastP	= d + 0.1

				# Maximum or average sample height
				if mx:
					TrHt	= OST50.min
				else:
					TrHt	= 0
					nTr	= 0

			stD	= d / nS
			stX	= (x2 - x1) / nS
			stY	= (y2 - y1) / nS

			# Set up return values
			minEl	= OST50.max
			maxEl	= OST50.min
			average	= 0
			minGr	= float("Infinity")
			maxGr	= float("-Infinity")
			aveGr	= 0
			ascent	= 0
			level	= 0
			descent	= 0
			surface	= 0
			lastTr	= None

			for i in range( 0, nS + 1 ):
				s	= i*stD
				en	= Duple( x1 + i*stX, y1 + i*stY )
				tr	= OST50.getHeight( en )

				# Minimum, maximum, and average elevations
				minEl	= min( tr, minEl )
				maxEl	= max( tr, maxEl )
				average	+= tr

				# Min and max gradients, totals for ascent, level, descent, and surface dist
				if (lastTr != None):
					lastTr	-= tr
					delta	= sqrt( dS**2 + lastTr**2 )
					surface += delta
					if lastTr < 0:
						ascent	+= delta
					if lastTr == 0:
						level	+= delta
					if lastTr > 0:
						descent	+= delta
					if dS:
						delta	= lastTr/dS
					elif lastTr:
						delta	= lastTr/OST50.cell
					else:
						delta	= 0
					minGr	= min( delta, minGr )
					maxGr	= max( delta, maxGr )
					aveGr	+= delta
				lastTr	= tr

				# Profile
				if pr:
					if i and abs( nextP*stP - s ) <= abs( s - thisP*stP ):
						if mx:
							point[len(point)-1] = int( round(TrHt) )
							TrHt	= OST50.min
						else:
							point[len(point)-1] = int( round(TrHt/nTr) )
							TrHt	= 0
							nTr	= 0
						points.append( point )
						if db:
							prof	+= "[%.0f,%.0f,%.0f],<br>\n" % (point[0], point[1], point[2])
						thisP	+= 1
						nextP	+= 1
						lastP	= d + 0.1

					if abs( s - thisP*stP ) < lastP:
						lastP = abs( s - thisP*stP )
						point = [ round(en.x, 6), round(en.y, 6), 0]

					# Maximum or average local sample height
					if mx:
						TrHt	= max( tr, TrHt )
					else:
						TrHt	+= tr
						nTr	+= 1

					if vb:
						prof	+= "/* {%d: %.3f,%.3f,%.0f} */<br>\n" % (i, en.x, en.y, tr)


			# If profile, finish last point
			if pr:
				if mx:
					point[len(point)-1] = int( round(TrHt) )
				else:
					point[len(point)-1] = int( round(TrHt/nTr) )
				points.append( point )
				if db:
					prof	+= "[%.0f,%.0f,%.0f]<br>\n],<br>\n" % (point[0], point[1], point[2])

			if vb:
				prof	+= " /* Tiles Used: %d */<br>\n" % ( Tiles )

			# Clean up values for output
			d	= round( d/1000, 3 )
			surface	= round( surface/1000, 3 )
			ascent	= round( ascent/1000, 3 )
			level	= round( level/1000, 3 )
			descent	= round( descent/1000, 3 )
			minEl	= int( floor(minEl) )
			maxEl	= int( ceil(maxEl) )
			average	= round( average/(nS + 1) )
			minGr	= round( minGr, 3 )
			maxGr	= round( maxGr, 3 )
			aveGr	= round( aveGr/nS, 3 )

			# Compile the output
			rs	= {}
			if db:
				resp	+= "{<br>\n"

			# Distances in km
			rs["dist"]	= d
			rs["surface"]	= surface
			rs["ascent"]	= ascent
			rs["level"]	= level
			rs["descent"]	= descent
			if db:
				resp	+= "\"dist\":%.3f,<br>\n" % rs["dist"]
				resp	+= "\"surface\":%.3f,<br>\n" % rs["surface"]
				resp	+= "\"ascent\":%.3f,<br>\n" % rs["ascent"]
				resp	+= "\"level\":%.3f,<br>\n" % rs["level"]
				resp	+= "\"descent\":%.3f,<br>\n" % rs["descent"]

			# Min and max elevations and gradients
			rs["min"] 	= minEl
			rs["max"] 	= maxEl
			rs["average"] 	= average
			rs["minGr"] 	= minGr
			rs["maxGr"] 	= maxGr
			rs["aveGr"] 	= aveGr
			if db:
				resp	+= "\"min\":%d,<br>\n" % ( rs["min"] )
				resp	+= "\"max\":%d,<br>\n" % ( rs["max"] )
				resp	+= "\"average\":%d,<br>\n" % ( rs["average"] )
				resp	+= "\"minGr\":%.3f,<br>\n" % ( rs["minGr"] )
				resp	+= "\"maxGr\":%.3f,<br>\n" % ( rs["maxGr"] )
				resp	+= "\"aveGr\":%.3f%s<br>\n" % ( rs["aveGr"], "," if pr else "" )

			# If profile, insert the profile data array and the Google Static Image Charts URL
			if pr:
				rs["prof"] = points
				if db:
					resp	+= prof

				# Google Static Image Charts URL
				# See https://developers.google.com/chart/image/docs/chart_params

				maxEl	+= 1
				src	= ""	# "&lt;placeholder&gt;"

				# Curvature (optional), terrain
				for i in range( len(rs["prof"][0])-1, 1, -1 ):
					for j in range( 0, len(rs["prof"]) ):
						if (j == 0) and (src != ""):
							src += ","
						try:
							src += extEncodeGoogle( max(minEl,rs["prof"][j][i]), minEl, maxEl )
						except Exception:
							src += "__"
							if vb:
								resp += "/* !!!Error!!!  -  i: %u, j: %u, p: %u */<br>\n" % ( i, j, rs["prof"][j][i] )

				src = "http://chart.apis.google.com/chart" \
				+ "?chs=~Width~x~Height~" \
				+ "&chma=" + ("35" if maxEl >= 1000 else "30") + "," + ("35" if maxEl*FPM >= 1000 else "30") + ",22,23" \
				+ "&cht=lc" \
				+ "&chd=e:" \
					+ src + "," \
					+ extEncodeGoogle( minEl, minEl, maxEl ) + extEncodeGoogle( minEl, minEl, maxEl ) \
				+ "&chf=bg,s," + white +"|c,lg,90," + sky1 + ",1," + sky2 + ",0" \
				+ "&chco=" \
					+ terr + "," \
					+ axes \
				+ "&chm=" \
					+ "b," + terr + ",0,1,0" \
				+ "&chxt=x,y,t,r" \
				+ "&chxs=" \
					+ "0," + axes + "," + font + ",0,lt," + axes + "," + axes \
					+ "|1," + axes + "," + font + ",1,lt," + axes + "," + axes \
					+ "|2," + axes + "," + font + ",0,lt," + axes + "," + axes \
					+ "|3," + axes + "," + font + ",-1,lt," +axes + "," +  axes \
				+ "&chxtc=0," + str(thick) + "|1," + str(thick) + "|2," + str(thick) + "|3," + str(thick)

				# Axis scales
				scale	= d
				interval = axisStep( scale, 5 )
				src	+= "&chxr=0,0," + str( round(scale,2) ) + "," + str( interval )

				scale	= MPK*d
				interval = axisStep( scale, 5 )
				src	+= "|2,0," + str( round(scale,2) )  + "," + str( interval )

				scale	= maxEl - minEl
				interval = max( int(round( axisStep(scale, 3) )), 1 )
				chxl	= "&chxl=0:|km|1:|m"
				chxp	= "&chxp=1,0"
				for i in range( int(floor(minEl/interval)*interval), maxEl + 1, interval ):
					if i > minEl + interval/3:
						chxl += "|" + str( i )
						chxp += "," + str( int( round(100*(i-minEl)/scale) ) )

				minEl	= int( round(minEl*FPM) )
				maxEl	= int( round(maxEl*FPM) )
				scale	= maxEl - minEl
				interval = max( int(round( axisStep(scale, 3) )), 1 )
				chxl	+= "|2:|mi|3:|ft"
				chxp	+= "|3,0"
				for i in range( int(floor(minEl/interval)*interval), maxEl, interval ):
					if i > minEl + interval/3:
						chxl += "|" + str( i )
						chxp += "," + str( int( round(100*(i-minEl)/scale) ) )
				src	+= chxl + chxp

				rs["url"] = src
				src	= src.replace("&", "&amp;").replace("|", "%7C")
				if db:
					resp	+= "\"url\":\"%s\"<br>\n" % src

			if db:
				resp	+= "}"
			else:
				resp	= json.dumps( rs, separators=(",",":"), sort_keys=True )

			if cb:
				resp	= cb + "(" + resp + ");"

			if db:
				resp	= "<p>Output:<br>\n" + resp + "</p>\n"
				if pr:
					resp	+= "<img src=\"%s\" style=\"width:180mm; height:60mm;\" alt=\"Google Static Image Chart\">\n"  % ( src.replace("~Width~", "680").replace("~Height~", "227") )

				t1	= datetime.now()
				tt	= t1 - t0
				resp	+= "<p>Time taken: %0.3fs</p>\n" % (tt.seconds + float(tt.microseconds)/1000000)

			error	= False

		elif (x1 != None) and (x1 >= XMin) and (x1 < XMax) and (y1 != None) and (y1 >= YMin) and (y1 < YMax) and (x2 == None) and (y2 == None):

			if db:
				t0	= datetime.now()

			# Get spot height for a single point
			tr	= OST50.getHeight( Duple(x1, y1) )
			resp	+= "{\"ht\":%.0f}" % ( tr )

			if cb:
				resp	= cb + "(" + resp + ");"

			# Compile the output
			if db:
				t1	= datetime.now()
				tt	= t1 - t0
				resp	= "<p>Output:&nbsp; %s</p>\n<p>Time taken: %0.3fs</p>\n" % ( resp, tt.seconds + float(tt.microseconds)/1000000 )

			error	= False

		else:
			error	= True

		if db or error:
			version	= os.path.basename( sys.argv[0] ) + " v2.5, " \
					+ datetime.utcfromtimestamp( os.path.getmtime(sys.argv[0]) ).isoformat( " " )
			page	= PageTitle + " - " + version

			if error:
				resp += "<h2>HELP</h2>\n" \
				+ "<p>This program calculates from <a href=\"http://www.ordnancesurvey.co.uk/business-and-government/products/terrain-50.html\" title=\"www.ordnancesurvey.co.uk\" target=\"_blank\">Ordnance Survey Terrain 50</a> data either the elevation of a single point or an elevation profile between two points.</p>\n" \
				+ "<p>When called successfully, the data returned as <abbr title=\"JavaScript Object Notation\">JSON</abbr> is as follows:<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;Either:<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>ht</strong>&nbsp;&nbsp;Point elevation (metres referenced to <abbr title=\"Ordnance Survey Geoid Model 2 (almost sea-level)\">OSGM02</abbr>)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;Or:<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>dist</strong>&nbsp;&nbsp;Horizontal distance (kilometres)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>surface</strong>&nbsp;&nbsp;Surface distance over the actual terrain (kilometres)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>ascent</strong>&nbsp;&nbsp;Surface distance ascending (kilometres)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>level</strong>&nbsp;&nbsp;Surface distance level (kilometres)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>descent</strong>&nbsp;&nbsp;Surface distance descending (kilometres)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>min</strong>&nbsp;&nbsp;Minimum elevation (metres referenced to OSGM02)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>max</strong>&nbsp;&nbsp;Maximum elevation (ditto)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>average</strong>&nbsp;&nbsp;Average elevation (ditto)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>minGr</strong>&nbsp;&nbsp;Minimum gradient (calculated as vertical change in height over horizontal distance)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>maxGr</strong>&nbsp;&nbsp;Maximum gradient (ditto)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>aveGr</strong>&nbsp;&nbsp;Average gradient (ditto)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>prof</strong>&nbsp;&nbsp;An array of profile points (optional)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>url</strong>&nbsp;&nbsp;Google Static Image Charts URL to obtain a profile diagram (optional)<br>\n" \
				+ "<p>For both a point height and a profile, location parameters must be given as&nbsp;&hellip;<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;<strong>x1</strong>&nbsp;&nbsp;Point or start easting (metres &gt;= %d and &lt; %d, referenced to OSGB36)<br>\n" % (XMin,XMax) \
				+ "&nbsp;&nbsp;&nbsp;<strong>y1</strong>&nbsp;&nbsp;Point or start northing (metres &gt;= %d and &lt; %d, ditto)<br>\n" % (YMin,YMax) \
				+ "&hellip;&nbsp;and additionally for a profile&nbsp;&hellip;<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;<strong>x2</strong>&nbsp;&nbsp;End easting (metres &gt;= %d and &lt; %d, ditto)<br>\n" % (XMin,XMax) \
				+ "&nbsp;&nbsp;&nbsp;<strong>y2</strong>&nbsp;&nbsp;End northing (metres &gt;= %d and &lt; %d, ditto)<br>\n" % (YMin,YMax) \
				+ "<p>Optionally, a callback function name can be given as <strong>cb</strong>.&nbsp; The data will then be returned as <span title=\"JSON with Padding\">JSONP</span> which can be loaded directly by a <code>&lt;script&gt;</code> tag</p>\n" \
				+ "<p>The following parameters are ignored if a second point is not given&nbsp;&hellip;</p>\n" \
				+ "<p>Optionally, use the parameter <strong>pr=true</strong> to obtain a full profile and a Google Static Image Charts URL for displaying it.</p>" \
				+ "<p>Optionally, use the parameter <strong>mx=true</strong> to have each sample point returned be the maximum, rather than by default the average, of the OST50 heights in the neighbourhood of the point.</p>" \
				+ "<p>The data columns returned in the optional profile array have the following meaning:<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;Easting (metres referenced to OSGB36)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;Northing (ditto)<br>\n" \
				+ "&nbsp;&nbsp;&nbsp;Average, or maximum if specified, height of local terrain (metres referenced to OSGM02)</p>\n" \
				+ "<p>Before submitting the URL to Google, <strong>~Width~</strong> and <strong>~Height~</strong> " \
				+ "must be replaced by suitable dimensions (pixels), the product (multiplication) of the two being less than Google's limit of 300,000&nbsp; -&nbsp; " \
				+ "see the <a href=\"https://developers.google.com/chart/image/\" title=\"developers.google.com\" target=\"_blank\">Google Static Image Charts API</a> for further information (this service has been deprecated since 2012 but thankfully is still running).</p>\n" \
				+ "<p>Optionally, use the parameter <strong>db=true</strong> " \
				+ "to obtain output as HTML as an aid to debugging.</p>\n"

				page	+= " - Error"
			else:
				page	+= " - Debug"

			template = open( os.path.join(os.path.dirname(__file__), TempPath), "r" ).read()
			resp = "Content-Type: text/html\n\n" \
				+ template.replace( "{{app}}", "MacFH<br>Ordnance Survey Terrain 50 Elevation Profiler" ) \
					.replace( "{{page}}", page ) \
					.replace( "{{x1}}", "%s"%x1 ) \
					.replace( "{{y1}}", "%s"%y1 ) \
					.replace( "{{x2}}", "%s"%x2 ) \
					.replace( "{{y2}}", "%s"%y2 ) \
					.replace( "{{cb}}", "%s"%cb ) \
					.replace( "{{pr}}", "%s"%pr ) \
					.replace( "{{mx}}", "%s"%mx ) \
					.replace( "{{db}}", "%s"%db ) \
					.replace( "{{resp}}", resp )
		else:
			resp = "Content-Type: application/json\n\n" + resp

		sys.stdout.write( resp )

main( cgi.FieldStorage() )
