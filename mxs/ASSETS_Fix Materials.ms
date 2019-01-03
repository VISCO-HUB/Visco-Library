/*  
[INFO] 

NAME = Fix Materials
VERSION = 1.1.1
AUTHOR = Vasily Lukyanenko
DEV = VISCO
HELP = 

[1.0.0]
* First release =

[1.1.0]
+ Added: Siger Shader plugins replace =


[1.1.1]
- BugFix: Small bug fixes =

[ABOUT]

This script helps to prepare materials for models before upload to Assets Library=

[TEST]

[SCRIPT]
*/	

try(closeRolloutFloater fAssetsLibraryFixMaterials)catch()
global fAssetsLibraryFixMaterials = newRolloutFloater "Fix Materials" 240 200

fn getScriptInfo s releaseInfo: "" =
(		
	if(releaseInfo != "") do
	(
		r = getINISetting s releaseInfo
		return r
	)
	
	v = getINISetting s "INFO" "VERSION"
	a = getINISetting s "INFO" "AUTHOR"
	n = getINISetting s "INFO" "NAME"
	o = getINISetting s "ABOUT"
	c = getINISetting s "INFO" "DEV"
	h = getINISetting s "INFO" "HELP"
	
	r = for i in (getINISetting s) where i != "ABOUT" and i != "SCRIPT" and i != "COPY" collect i
	
	return #(n, a, v, o, r, c, h)
)

rollout _rFixMaterials "Diffuse Gamma" 
(
	button btnSetGammaContrast "Set Gamma/Contrast" across: 2 align: #left
	spinner spnDiffuseGamma "" range: [0.1, 3.0, 0.45] type: #float fieldWidth: 50 align: #left offset: [29, 3]
	
	fn FixDiffuseGamma gamma: 0.45 =
	(
		mats = #()
		sel = selection as array

		if(sel.count == 0) do return MessageBox "Please select objects!" title: "Warning!"

		for o in sel where o.material != undefined do join mats (getClassInstances VRayMtl target:o.material)

		for m in mats do 
		(
			t_b = m.texmap_diffuse
			d = m.diffuse
						
			cc = undefined
			if(t_b != undefined and classOf t_b == ColorCorrection and t_b.name == "Gamma Fix") then
			(
				cc = t_b
			) else
			(
				cc = ColorCorrection()				
				cc.map = m.texmap_diffuse
				m.texmap_diffuse	= cc
			)
						
			cc.name = "Gamma Fix"
			cc.color = d
			cc.gammaRGB = gamma
			cc.lightnessMode = 1					
		)
		
		messageBox "Diffuse Gamma/Contrast Fixed!" title: "Done!"
	)
	
	on btnSetGammaContrast pressed do FixDiffuseGamma gamma: spnDiffuseGamma.value
)


rollout _rFixSigerShader "Siger Shaders" (
	button btnReplaceComplexFresnel "ComplexFresnel -> OSL (complex_ior.osl)"
	button btnReplaceThinFilm "ThinFilm -> OSL (irridescence.osl)"
	button btnReplaceProSimplex "ProSimplex -> Noise"
	
	fn ReplaceComplexFresnel = (
		if(ComplexFresnel == undefined) do return messageBox "Error! ComplexFresnel not installed!" title: "Error!"
		
		f = getFilenamePath (getThisScriptFileName()) 
		f += @"osl\complex_ior.osl"
			
		if(not doesFileExist f) do return messageBox "Error! File complex_ior.osl not found!" title: "Error!"
		
		m = #()
		m = for i in getClassInstances ComplexFresnel where i != undefined collect i
		m = makeUniqueArray m	
		
		cnt = 0
		
		progressStart "Convert ComplexFresnel..."
		escapeEnable
		
		for mm in m do (
			cnt += 1
			n = VRayOSLTex()
			n.shader_file = f
				
			n.n[1] = mm.nrvalue
			n.n[2] = mm.ngvalue
			n.n[3] = mm.nbvalue
			
			n.k[1] = mm.krvalue
			n.k[2] = mm.kgvalue
			n.k[3] = mm.kbvalue
			
			-- Add hue/sat/gamma  correction
			nn = ColorCorrection()
			nn.lightnessMode = 1
			nn.hueShift = mm.hue
			nn.saturation = mm.sat
			-- Calc inverse gamma
			g = 2.0 - mm.gamma
			if(g < 0.2) do g = 0.2
			nn.gammaRGB = g
			nn.map = n
			
			replaceInstances mm nn
			
			progressUpdate (100.0 * cnt / m.count)
			if(getProgressCancel() == true) do exit		
		)	
		
		progressEnd()
		
		messageBox "ComplexFresnel Replaced!" title: "Done!"
	)
	
	fn ReplaceThinFilm = (
		if(ThinFilm == undefined) do return messageBox "Error! ThinFilm not installed!" title: "Error!"
		
		f = getFilenamePath (getThisScriptFileName()) 
		f += @"osl\irridescence.osl"
			
		if(not doesFileExist f) do return messageBox "Error! File irridescence.osl not found!" title: "Error!"
		
		m = #()
		m = for i in getClassInstances ThinFilm where i != undefined collect i
		m = makeUniqueArray m	
		
		cnt = 0
		
		progressStart "Convert ThinFilm..."
		escapeEnable
		
		for mm in m do (
			cnt += 1
			n = VRayOSLTex()
			n.shader_file = f
				
			mm.thicknessmapenabled = true
			
			n.thicknessMin= mm.thicknessMin
			n.thicknessMax = 350.0 --mm.thicknessMax -- Mindfuck, max and min the same
			
			n.nmedium = mm.nmedium
			n.nfilm = mm.nfilm
			n.ninternal = mm.ninternal
			try(n.thickness = mm.thicknessmap) catch()
			
					
			-- Add hue/sat/gamma  correction
			nn = ColorCorrection()
			nn.lightnessMode = 1
			nn.hueShift = mm.hue
			nn.saturation = mm.sat
			-- Calc inverse gamma
			g = 2.0 - mm.gamma
			if(g < 0.2) do g = 0.2
			nn.gammaRGB = g
			nn.map = n
			
			replaceInstances mm nn
			
			progressUpdate (100.0 * cnt / m.count)
			if(getProgressCancel() == true) do exit		
		)	
		
		progressEnd()
		
		messageBox "ThinFilm Replaced!" title: "Done!"
	)
	
	fn ReplaceProSimplex = (
		if(ProSimplex == undefined) do return messageBox "Error! ProSimplex not installed!" title: "Error!"
		
		m = #()
		m = for i in getClassInstances ProSimplex where i != undefined collect i
		m = makeUniqueArray m	
		
		cnt = 0
		
		progressStart "Convert ProSimplex..."
		escapeEnable
		
		for mm in m do (
			cnt += 1
			n = Noise()
						
			n.size = mm.size / 10.0
			n.type = 1
			n.levels = 5.0
			n.phase = 1.0
			n.coords.blur = 0.01
			n.output.bump_amount = 2.0
				
			replaceInstances mm n
			
			progressUpdate (100.0 * cnt / m.count)
			if(getProgressCancel() == true) do exit		
		)	
		
		progressEnd()
		
		messageBox "ProSimplex Replaced!" title: "Done!"
	)
	
	on btnReplaceComplexFresnel pressed do ReplaceComplexFresnel()
	on btnReplaceThinFilm pressed do ReplaceThinFilm()
	on btnReplaceProSimplex pressed do ReplaceProSimplex()
)

rollout _rAbout "About" 
(
	label lblName "" 
	label lblVer "" 
	
	label lblAuthor "" height: 30
	label lblCopy ""  height: 30
	
	on _rAbout open do
	(		
		i = getScriptInfo (getThisScriptFilename())
			
		lblName.caption = i[1]
		lblAuthor.caption = i[2]
		lblVer.caption = i[3]
		lblCopy.caption = i[6]
	)
)

addRollout _rFixMaterials fAssetsLibraryFixMaterials rolledUp:false
addRollout _rFixSigerShader fAssetsLibraryFixMaterials rolledUp:false
addRollout _rAbout fAssetsLibraryFixMaterials rolledUp:true 